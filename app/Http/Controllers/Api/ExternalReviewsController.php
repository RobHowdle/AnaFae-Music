<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExternalReviewsController
{
    private const LMM_PROFILE_URL = 'https://www.lastminutemusicians.com/members/anafae.html';
    private const LMM_LISTING_ID = 21762;
    private const LMM_CAROUSELS_URL = 'https://www.lastminutemusicians.com/members/tmp.html';

    public function lastMinuteMusicians(Request $request)
    {
        $limit = (int) $request->query('limit', 8);
        $limit = max(1, min(20, $limit));

        $cacheKey = "external-reviews:lmm:anafae:v3:limit={$limit}";

        try {
            $reviews = Cache::remember($cacheKey, now()->addHours(6), function () use ($limit) {
                $json = $this->fetchLmmCarouselsJson();
                $carouselHtml = (string) ($json['reviewsCarousel'] ?? '');
                if (!$carouselHtml) {
                    return [];
                }

                $parsed = $this->extractReviewsFromCarouselHtml($carouselHtml);
                $parsed = array_slice($parsed, 0, $limit);

                $out = [];
                foreach ($parsed as $idx => $r) {
                    $out[] = [
                        'id' => $idx + 1,
                        'url' => self::LMM_PROFILE_URL,
                        'text' => (string) ($r['text'] ?? ''),
                        'author' => $r['author'] ?? null,
                        'reviewedOn' => null,
                        'rating' => $r['rating'] ?? null,
                    ];
                }

                return $out;
            });

            return response()->json([
                'source' => 'lastminutemusicians',
                'profileUrl' => self::LMM_PROFILE_URL,
                'reviews' => $reviews,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'source' => 'lastminutemusicians',
                'profileUrl' => self::LMM_PROFILE_URL,
                'reviews' => [],
                'error' => 'Failed to load reviews from upstream.',
            ], 502);
        }
    }

    private function fetchHtml(string $url): string
    {
        /** @var Response $response */
        $response = Http::timeout(12)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; AnaFaeMusicBot/1.0)',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ])
            ->get($url);

        $response->throw();

        return (string) $response->body();
    }

    private function fetchLmmCarouselsJson(): array
    {
        /** @var Response $response */
        $response = Http::timeout(12)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; AnaFaeMusicBot/1.0)',
                'Accept' => 'application/json,text/plain,*/*',
                'Referer' => self::LMM_PROFILE_URL,
            ])
            ->get(self::LMM_CAROUSELS_URL, [
                'c' => 'getCarouselsJson',
                'id' => self::LMM_LISTING_ID,
            ]);

        $response->throw();

        $data = $response->json();
        return is_array($data) ? $data : [];
    }

    /**
     * @return array<int, array{text: string, author: ?string, rating: ?int}>
     */
    private function extractReviewsFromCarouselHtml(string $html): array
    {
        $html = trim($html);
        if ($html === '') {
            return [];
        }

        $dom = new \DOMDocument();
        $prevUseErrors = libxml_use_internal_errors(true);
        try {
            // Wrap fragment so DOMDocument can parse reliably.
            $dom->loadHTML(
                '<!doctype html><html><meta http-equiv="content-type" content="text/html; charset=utf-8"><body>' . $html . '</body></html>',
                LIBXML_NOWARNING | LIBXML_NOERROR,
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($prevUseErrors);
        }

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//blockquote');
        if (!$nodes) {
            return [];
        }

        $seen = [];
        $out = [];

        foreach ($nodes as $blockquote) {
            $text = trim((string) $xpath->evaluate('string(.//em)', $blockquote));
            if ($text === '') {
                $text = trim((string) $xpath->evaluate('string(.)', $blockquote));
            }

            $text = $this->normalizeWhitespace($text);
            $text = preg_replace('/^[\"“”]+|[\"“”]+$/u', '', $text);
            $text = trim((string) $text);

            if ($text === '') {
                continue;
            }

            // Filter out accidental "Rating: 10 / 10" noise if it slips in.
            $text = preg_replace('/\s*Rating:\s*\d{1,2}\s*\/\s*10\s*$/u', '', $text);
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }

            if (isset($seen[$text])) {
                continue;
            }

            $ratingRaw = trim((string) $xpath->evaluate("string(.//footer//*[contains(concat(' ', normalize-space(@class), ' '), ' pink-text ')])", $blockquote));
            if ($ratingRaw === '') {
                $ratingRaw = trim((string) $xpath->evaluate('string(.//footer)', $blockquote));
            }

            $rating = null;
            if (preg_match('/(\d{1,2})\s*\/\s*10/u', $ratingRaw, $m)) {
                $rating = (int) $m[1];
            }

            $authorRaw = trim((string) $xpath->evaluate('string(.//footer//div[1])', $blockquote));
            $authorRaw = preg_replace('/\b\d{1,2}\s*\/\s*10\b/u', '', (string) $authorRaw);
            $author = $this->normalizeWhitespace((string) $authorRaw);
            if ($author === '') {
                $author = null;
            }

            $seen[$text] = true;
            $out[] = [
                'text' => $text,
                'author' => $author,
                'rating' => $rating,
            ];
        }

        return $out;
    }

    private function extractFirstReviewId(string $profileHtml): ?int
    {
        if (preg_match('/review-centre%3FreviewId%3D(\d+)/i', $profileHtml, $m)) {
            return (int) $m[1];
        }

        if (preg_match('/review-centre\?reviewId=(\d+)/i', $profileHtml, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function extractPrevReviewId(string $reviewHtml): ?int
    {
        if (preg_match('/href="[^"]*review-centre\?reviewId=(\d+)[^"]*"[^>]*>\s*Previous\s*</i', $reviewHtml, $m)) {
            return (int) $m[1];
        }

        // Fallback: if we can't find a specific "Previous" link, don't continue crawling.
        return null;
    }

    private function parseReviewPage(string $reviewHtml, int $reviewId, string $reviewUrl): ?array
    {
        $text = $this->htmlToText($reviewHtml);

        $reviewText = null;
        $author = null;
        $rating = null;
        $reviewedOn = null;

        if (preg_match('/Reviewed on\s+([^\n]+?)\s+by\s+([^\n]+?)\s*\n/i', $text, $m)) {
            $reviewedOn = trim($m[1]);
            // This is sometimes the same as the author line; keep both if present.
            $author = trim($m[2]);
        }

        if (preg_match('/Rating:\s*([0-9]{1,2})\s*\/\s*10/i', $text, $m)) {
            $rating = (int) $m[1];
        }

        // Reviews frequently use curly quotes (“ ”) after HTML entity decoding.
        // Also tolerate different dash separators.
        $quoteOpen = '["“]';
        $quoteClose = '["”]';
        $dash = '[—–-]';

        if (preg_match("/{$quoteOpen}\s*(.*?)\s*{$quoteClose}\s*{$dash}\s*(.*?)\s*Rating:\\s*([0-9]{1,2})\\s*\\/\\s*10/su", $text, $m)) {
            $reviewText = $this->normalizeWhitespace($m[1]);
            $authorFromDash = $this->normalizeWhitespace($m[2]);
            if ($authorFromDash) {
                $author = $authorFromDash;
            }
            $rating = (int) $m[3];
        } elseif (preg_match("/{$quoteOpen}\s*(.*?)\s*{$quoteClose}\s*{$dash}\s*(.*?)(Share this review:|SEE MORE REVIEWS|\\n)/su", $text, $m)) {
            $reviewText = $this->normalizeWhitespace($m[1]);
            $authorFromDash = $this->normalizeWhitespace($m[2]);
            if ($authorFromDash) {
                $author = $authorFromDash;
            }
        }

        if (!$reviewText) {
            return null;
        }

        return [
            'id' => $reviewId,
            'url' => $reviewUrl,
            'text' => $reviewText,
            'author' => $author,
            'reviewedOn' => $reviewedOn,
            'rating' => $rating,
        ];
    }

    private function htmlToText(string $html): string
    {
        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $stripped = strip_tags($decoded);

        // Keep some structure for regex parsing.
        $stripped = str_replace(["\r\n", "\r"], "\n", $stripped);
        $stripped = preg_replace('/\n{3,}/', "\n\n", $stripped);

        return (string) $stripped;
    }

    private function normalizeWhitespace(string $value): string
    {
        $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);
        return trim((string) $value);
    }
}

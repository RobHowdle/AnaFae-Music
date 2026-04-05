import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{js,vue}",
    ],

    theme: {
        extend: {
            colors: {
                softLavender: "#EADAE6",
                deepPurple: "#6C4A72",
                mutedLilac: "#A27BA0",
                pastelBlue: "#B7C9E2",
                softSageGreen: "#C8CE9A",
                lightGreenTint: "#E2E5C9",
            },
            fontFamily: {
                sans: ["Montserrat", ...defaultTheme.fontFamily.sans],
                heading: [
                    "Cormorant Garamond",
                    ...defaultTheme.fontFamily.serif,
                ],
            },
        },
    },

    plugins: [forms],
};

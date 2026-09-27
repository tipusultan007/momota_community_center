import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    corePlugins: {
        collapse: false,
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Anek Bangla', 'Inter', ...defaultTheme.fontFamily.sans],
                headline: ['Anek Bangla', 'Manrope', ...defaultTheme.fontFamily.sans],
                body: ['Anek Bangla', 'Inter', ...defaultTheme.fontFamily.sans],
                label: ['Anek Bangla', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                DEFAULT: '0.125rem',
                lg: '0.25rem',
                xl: '0.5rem',
                full: '0.75rem',
            },
            colors: {
                // Core brand
                "primary":                      "#0F172A",
                "on-primary":                   "#ffffff",
                "primary-container":            "#1e293b",
                "on-primary-container":         "#cbd5e1",
                "primary-fixed":                "#f1f5f9",
                "primary-fixed-dim":            "#e2e8f0",
                "on-primary-fixed":             "#0F172A",
                "on-primary-fixed-variant":     "#334155",
                "inverse-primary":              "#94a3b8",

                // Secondary (Emerald)
                "secondary":                    "#10B981",
                "on-secondary":                 "#ffffff",
                "secondary-container":          "#d1fae5",
                "on-secondary-container":       "#065f46",
                "secondary-fixed":              "#a7f3d0",
                "secondary-fixed-dim":          "#6ee7b7",
                "on-secondary-fixed":           "#064e3b",
                "on-secondary-fixed-variant":   "#059669",

                // Tertiary (Gold)
                "tertiary":                     "#D4AF37",
                "on-tertiary":                  "#ffffff",
                "tertiary-container":           "#fef3c7",
                "on-tertiary-container":        "#92400e",
                "tertiary-fixed":               "#fde68a",
                "tertiary-fixed-dim":           "#fbbf24",
                "on-tertiary-fixed":            "#78350f",
                "on-tertiary-fixed-variant":    "#b45309",

                // Neutral
                "neutral":                      "#F8FAFC",
                "on-neutral":                   "#0F172A",

                // Error
                "error":                        "#ba1a1a",
                "on-error":                     "#ffffff",
                "error-container":              "#ffdad6",
                "on-error-container":           "#93000a",

                // Background / Surface (Clean Navy/Slate theme, NOT blue)
                "background":                   "#F8FAFC",
                "on-background":                "#0F172A",
                "surface":                      "#ffffff",
                "on-surface":                   "#0F172A",
                "surface-variant":              "#e2e8f0",
                "on-surface-variant":           "#334155",
                "surface-bright":               "#ffffff",
                "surface-dim":                  "#cbd5e1",
                "surface-container-lowest":     "#ffffff",
                "surface-container-low":        "#f7f9fb",
                "surface-container":            "#f1f5f9",
                "surface-container-high":       "#e2e8f0",
                "surface-container-highest":    "#cbd5e1",
                "surface-tint":                 "#0F172A",

                // Utility
                "outline":                      "#94a3b8",
                "outline-variant":              "#cbd5e1",
                "inverse-surface":              "#0F172A",
                "inverse-on-surface":           "#F8FAFC",
            },
        },
    },

    plugins: [forms],
};

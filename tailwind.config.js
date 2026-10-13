/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        navy: {
          DEFAULT: '#0B2545',
          dark: '#07172C',
          deep: '#040E1B',
          light: '#13355F',
          muted: '#1E406D',
        },
        cyan: {
          DEFAULT: '#0099FF',
          vibrant: '#00A8FF',
          light: '#38BDF8',
          glow: 'rgba(0, 153, 255, 0.3)',
          subtle: '#E0F2FE',
        },
        gold: {
          DEFAULT: '#F59E0B',
          light: '#FEF3C7',
        },
        cream: {
          DEFAULT: '#F8FAFC',
          soft: '#F1F5F9',
          tint: '#F0F6FF',
        }
      },
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        display: ['"Outfit"', '"Plus Jakarta Sans"', 'sans-serif'],
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(11, 37, 69, 0.08)',
        'glass-dark': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
        'cyan-glow': '0 10px 40px rgba(0, 153, 255, 0.25)',
        'deep': '0 25px 60px -15px rgba(11, 37, 69, 0.18)',
      },
      borderRadius: {
        '4xl': '2rem',
      }
    },
  },
  plugins: [],
}

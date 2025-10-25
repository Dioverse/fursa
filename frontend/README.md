# Project Name

> A modern Vue.js application built with Yarn

## Description

Brief description of what your application does and its main purpose. Explain the problem it solves or the functionality it provides.

## Features

- ✨ Modern Vue.js architecture
- 🎨 Responsive design
- ⚡ Fast development with Vite
- 📦 Package management with Yarn
- 🔧 ESLint and Prettier for code quality

## Prerequisites

Before you begin, ensure you have the following installed:

- [Node.js](https://nodejs.org/) (version 16.x or higher)
- [Yarn](https://yarnpkg.com/) (version 1.22.x or higher)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/yourusername/your-project-name.git
cd your-project-name
```

2. Install dependencies:

```bash
yarn install
```

## Development

To start the development server:

```bash
yarn dev
```

The application will be available at `http://localhost:5173` (or another port if 5173 is in use).

## Building for Production

To build the application for production:

```bash
yarn build
```

The built files will be in the `dist` directory.

## Preview Production Build

To preview the production build locally:

```bash
yarn preview
```

## Scripts

- `yarn dev` - Start development server
- `yarn build` - Build for production
- `yarn preview` - Preview production build
- `yarn lint` - Run ESLint
- `yarn lint:fix` - Run ESLint and fix issues
- `yarn format` - Format code with Prettier

## Project Structure

```
src/
├── components/          # Reusable Vue components
├── views/              # Page components
├── router/             # Vue Router configuration
├── stores/             # Pinia stores (state management)
├── assets/             # Static assets
├── styles/             # Global styles
└── utils/              # Utility functions
```

## Technologies Used

- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router** - Official router for Vue.js
- **Pinia** - State management library
- **Vite** - Build tool and development server
- **Yarn** - Package manager
- **ESLint** - Code linting
- **Prettier** - Code formatting

## Configuration

### Environment Variables

Create a `.env` file in the project root (alongside `package.json`):

```env
VITE_API_BASE_URL=https://api.example.com
VITE_APP_TITLE=Your App Name
# Optional: override brochure download URL. If unset, the app will use /brochure.pdf from public/
VITE_BROCHURE_URL=https://cdn.example.com/files/mrs-brochure.pdf
```

If you don't set `VITE_BROCHURE_URL`, place your brochure PDF at `public/brochure.pdf` so the Brochure component can serve it by default.

## Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

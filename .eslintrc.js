module.exports = {
    root: true,
    parserOptions: {
        "parser": "@typescript-eslint/parser"
    },
    env: {
        es6: true,
        node: true,
        browser: true
    },
    extends: [
        "eslint:recommended",
        "google",
    ],
    globals: {
    },
    rules: {
        "max-len": "off",
        "complexity": ["error", 10],
        "no-console": ["warn", { "allow": ["warn", "error"] }],
    }
};

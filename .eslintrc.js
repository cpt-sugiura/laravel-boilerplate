module.exports = {
  env: {
    browser: true,
    es6: true,
    node: true,
    jest: true,
  },
  extends: [
    'eslint:recommended',
    'plugin:react/recommended',
    'plugin:@typescript-eslint/eslint-recommended',
    'plugin:@typescript-eslint/recommended',
    'google',
    'plugin:prettier/recommended',
  ],
  settings: {
    react: {
      version: 'detect',
    },
  },
  parser: '@typescript-eslint/parser',
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    ecmaVersion: 2018,
    sourceType: 'module',
  },
  plugins: ['react', '@typescript-eslint', 'import-access'],
  rules: {
    'require-jsdoc': 'off',
    'valid-jsdoc': 'off',
    'complexity': ['error', 20],
    'no-console': ['error', {allow: ['warn', 'error']}],
    'react/jsx-uses-react': 'error',
    'react/jsx-uses-vars': 'error',
    'react/prop-types': 'off',
    'no-invalid-this': 'off',
    'camelcase': 'off',
    'prettier/prettier': [
      'error',
      {
        printWidth: 120, // 行の最大長
        tabWidth: 2, // 1 インデントあたりの空白数
        useTabs: false,
        semi: true, // 式の最後にセミコロンを付加する
        singleQuote: true, // 引用符としてシングルクオートを使用する
      },
    ],
    '@typescript-eslint/ban-types': 'off',
    'import-access/jsdoc': ['error'],
  },
};

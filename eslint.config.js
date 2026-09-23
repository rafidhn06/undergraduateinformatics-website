import js from '@eslint/js';
import tseslint from 'typescript-eslint';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';
import eslintConfigPrettier from 'eslint-config-prettier';

export default tseslint.config(
    { ignores: ['public', 'bootstrap', 'storage', 'vendor', 'node_modules', 'resources/js/routeTree.gen.ts'] },
    {
        extends: [
            js.configs.recommended,
            ...tseslint.configs.recommended,
        ],
        files: ['resources/js/**/*.{ts,tsx}'],
        languageOptions: {
            ecmaVersion: 2020,
        },
        plugins: {
            'react-hooks': reactHooks,
            'react-refresh': reactRefresh,
        },
        rules: {
            ...reactHooks.configs.recommended.rules,
            'react-refresh/only-export-components': [
                'warn',
                { allowConstantExport: true, extraHOCs: ['createLink'] },
            ],
            '@typescript-eslint/no-explicit-any': 'error',
        },
    },
    {
        files: [
            'resources/js/**/*.test.{ts,tsx}',
            'resources/js/test/**/*.{ts,tsx}',
            'resources/js/**/*.stories.tsx',
        ],
        rules: {
            '@typescript-eslint/no-explicit-any': 'off',
        },
    },
    {
        files: ['resources/js/routes/**/*.{ts,tsx}'],
        rules: {
            'react-refresh/only-export-components': 'off',
        },
    },
    eslintConfigPrettier,
);

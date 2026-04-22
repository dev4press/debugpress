import wordpress from '@wordpress/eslint-plugin';

export default [
    {
        ignores: [
            'node_modules/**',
            'vendor/**',
            'dist/**',
            'build/**',
            '**/*.min.js',
        ],
    },
    {
        files: [ 'js/**/*.js' ],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'script',
            globals: {
                $: 'readonly',
                jQuery: 'readonly',
                wp: 'readonly',
                ajaxurl: 'readonly',
                ClipboardJS: 'readonly',
                google: 'readonly',
                monthSelectPlugin: 'readonly',
                window: 'readonly',
                document: 'readonly',
                confirm: 'readonly',
            },
        },
        plugins: {
            '@wordpress': wordpress,
        },
        rules: {
            'no-unused-vars': [ 'error', { argsIgnorePattern: '^_' } ],
            'no-undef': 'error',
        },
    },
];
export default {
    extends: [
        'stylelint-config-standard-scss',
        '@wordpress/stylelint-config'
    ],
    ignoreFiles: [
        '**/*.min.css',
        '**/vendor/**',
        '**/node_modules/**',
    ],
    rules: {
        'at-rule-no-unknown': null,
        'scss/at-rule-no-unknown': true,

        'selector-class-pattern': null,
        'selector-id-pattern': null,

        'max-nesting-depth': 8,
        'no-descending-specificity': null,

        'rule-empty-line-before': null,

        'font-family-no-missing-generic-family-keyword': null,
        'no-duplicate-selectors': null,
    },
};
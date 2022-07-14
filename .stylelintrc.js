module.exports = {
    "extends": "stylelint-config-standard-scss",
    "rules": {
        "selector-class-pattern": null, // ケバブケースで引っかかる
        "scss/dollar-variable-pattern": null, // ケバブケースで引っかかる
        "no-duplicate-selectors": null,
        "no-descending-specificity": null,// 後から弱いセレクターを書くべきでない
        "declaration-block-no-redundant-longhand-properties": null, // grid を短縮で書けとかそういうやつ
    }
}

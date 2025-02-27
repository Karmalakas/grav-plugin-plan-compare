const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        if (mutation.type !== "attributes" || mutation.target.getAttribute("data-action") !== "collapse") {
            return;
        }

        const li = mutation.target.closest("li");
        const editor = li?.querySelector(".CodeMirror ").CodeMirror;

        editor && editor.refresh();
    });
});

$(document).ready(() => {
    const target = document.querySelectorAll(".form-columns.grid .item-actions > i");

    target.forEach(el => {
        observer.observe(el, {attributes: true})
    })
});
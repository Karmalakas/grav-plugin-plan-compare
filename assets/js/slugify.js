const Regenerate = (slugElement, labelElement) => {
    slugElement
        .addClass('highlight')
        .val($.slugify(labelElement.val(), {custom: {"'": ''}}));

    setTimeout(() => slugElement.removeClass('highlight'), 500);
};

$(document)
    .on('click', '[data-plan-compare-slug-regenerate]', (event) => {
        event.preventDefault();

        const target = $(event.currentTarget);
        const slugField = $(target.prev());
        const labelField = $(`[name="${slugField.attr("name").replace('\[slug\]', '[label]')}"]`);

        Regenerate(slugField, labelField);
    })
    .on('input', '[data-plan-compare-label]', (event) => {
        if (event.target.defaultValue) {
            return;
        }

        const target = $(event.currentTarget);
        const slugField = $(`[name="${target.attr("name").replace('\[label\]', '[slug]')}"]`);

        Regenerate(slugField, target);
    })
;

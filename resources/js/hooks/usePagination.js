import { computed } from "vue";
export function useMaxPages(
    totalPages,
    currentPage,
    maxPages = 6
) {
    const pages = computed(() => {
        let startPage, endPage;
        if (totalPages.value <= maxPages) {
            // total pages less than max so show all pages
            startPage = 1;
            endPage = totalPages.value;
        } else {
            // total pages more than max so calculate start and end pages
            const maxPagesBeforeCurrentPage = Math.floor(maxPages / 2);
            const maxPagesAfterCurrentPage = Math.ceil(maxPages / 2) - 1;
            if (currentPage.value <= maxPagesBeforeCurrentPage) {
                // current page near the start
                startPage = 1;
                endPage = maxPages;
            } else if (currentPage.value + maxPagesAfterCurrentPage >= totalPages.value) {
                // current page near the end
                startPage = totalPages.value - maxPages + 1;
                endPage = totalPages.value;
            } else {
                // current page somewhere in the middle
                startPage = currentPage.value - maxPagesBeforeCurrentPage;
                endPage = currentPage.value + maxPagesAfterCurrentPage;
            }
        }
        return { start: startPage, end: endPage };
    });
    return pages;
}
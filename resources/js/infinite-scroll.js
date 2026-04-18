// resources/js/infinite-scroll.js

export default function infiniteScroll(initialHasMore) {
    return {
        page: 1,
        loading: false,
        hasMore: initialHasMore,

        init() {
            const observer = new IntersectionObserver(
                (entries) => {
                    if (
                        entries[0].isIntersecting &&
                        !this.loading &&
                        this.hasMore
                    ) {
                        this.fetchNextPage();
                    }
                },
                {
                    threshold: 0.1,
                },
            );

            observer.observe(this.$refs.loadMore);
        },

        async fetchNextPage() {
            this.loading = true;
            this.page++;

            const url = new URL(window.location.href);
            url.searchParams.set("page", this.page);

            try {
                const response = await fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                const html = await response.text();

                if (html.trim() === "") {
                    this.hasMore = false;
                } else {
                    this.$refs.container.insertAdjacentHTML("beforeend", html);
                }
            } catch (error) {
                console.error("読み込みに失敗しました", error);
            } finally {
                this.loading = false;
            }
        },
    };
}

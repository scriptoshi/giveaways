<template>
    <div v-if="src" :style="styles.widthLimitter">
        <div :style="styles.renderingAreaProvider">
            <iframe
                :src="src"
                :style="styles.iframe"
                frameborder="0"
                allowfullscreen="true"
            ></iframe>
        </div>
    </div>
</template>
<script>
export default {
    props: ["maxWidth", "videoId"],
    computed: {
        src() {
            if (this.videoId.includes("https://")||this.videoId.includes("youtu")){
                const rx = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|&v(?:i)?=))([^#&?]*).*/;
                const id = this.videoId.match(rx);
                const videoId =   id?.[1];
                if(!videoId) return null;
                return `https://www.youtube.com/embed/${videoId}`;
            }
            return `https://www.youtube.com/embed/${this.videoId}`;
        },
    },
    data() {
        return {
            styles: {
                widthLimitter: {
                    maxWidth: this.maxWidth ? this.maxWidth : null,
                },
                renderingAreaProvider: {
                    background: "#f0f0f0",
                    height: 0,
                    margin: "1rem 0",
                    /*
                     * - '56.25%' indicates the aspect rasio (9/16 = 56.25%).
                     * - note that percentage inside 'padding-(top|bottom)' is calculated based on
                     *   its current width. this is a specification of 'calc' used inside
                     *   the 'padding-(top|bottom)' property.
                     *
                     * see: https://nathan.gs/2018/01/07/responsive-slideshare-iframe/
                     */
                    paddingBottom: "calc(56.25%)",
                    position: "relative",
                    width: "100%",
                },
                iframe: {
                    height: "100%",
                    left: 0,
                    position: "absolute",
                    top: 0,
                    width: "100%",
                },
            },
        };
    },
};
</script>

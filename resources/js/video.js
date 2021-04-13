import Vue from 'vue';
import VideoPlayer from "./components/VideoPlayer.vue";

Vue.config.devtools = true;

new Vue({
    el: "#video",

    components: {
        VideoPlayer
    },

    data: {
        videoOptions: {
            autoplay: true,
            controls: true,
            sources: [
                {
                    src: `/vids/Video-1.mp4`,
                    type: "video/mp4"
                }
            ],
            height: 720,
            width: 1024
        },
        videoSelecting: false,
        selectedVideo: {id: 0, name: "Video1", url: `/vids/Video-1.mp4`, isActive: true},
        videoList: [
            {id: 0, name: "Video1", url: `/vids/Video-1.mp4`, isActive: true},
            {id: 1, name: "Video2", url: `/vids/Video-2.mp4`, isActive: false},
            {id: 2, name: "Video3", url: `/vids/Video-3.mp4`, isActive: false},
            {id: 3, name: "Video4", url: `/vids/Video-4.mp4`, isActive: false},
            {id: 4, name: "Video5", url: `/vids/Video-5.mp4`, isActive: false},
        ]
    },

    watch: {
        selectedVideo(val){
            this.videoOptions.sources[0].src = val.url;
        }
    },

    methods:{
        setVideo(item,index){
            if(this.selectedVideo.id == index){
                return;
            }
            this.videoSelecting = true;
            this.emptyVideo().then(() => {
                this.selectedVideo = item;
                item.isActive = true;
                this.videoSelecting = false;
            });
        },
        emptyVideo(){
            return new Promise((resolve, reject) => {
                this.selectedVideo = null,
                resolve();
            });
        }
    },

});
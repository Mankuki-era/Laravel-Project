<template>
  <div>
    <div v-if="indexPage">
      <button v-if="!liked" type="button" @click="like(postId)">
        <i class="far fa-heart heart-icon"></i><span class="good">{{ likeCount }}</span>
      </button>
      <button v-else type="button" @click="unlike(postId)">
        <i class="fas fa-heart heart-icon red-icon"></i><span class="good">{{ likeCount }}</span>
      </button>
    </div>
    <div v-else>
      <button v-if="!liked" type="button" @click="like(postId)">
        <i class="far fa-heart fa-lg heart-icon"></i><span class="good">{{ likeCount }}</span>
      </button>
      <button v-else type="button" @click="unlike(postId)">
        <i class="fas fa-heart fa-lg heart-icon red-icon"></i><span class="good">{{ likeCount }}</span>
      </button>
    </div>
  </div>
</template>

<script>
    export default {
      props: ['postId', 'userId', 'defaultCount', 'defaultLiked', 'indexPage'],
      data() {
        return {
          likeCount: 0,
          liked: false
        };
      },
      created() {
        this.likeCount = this.defaultCount,
        this.liked = this.defaultLiked
      },
      methods: {
        like(postId) {
          let url = `/api/posts/${postId}/like`

          axios.post(url, {
            user_id: this.userId
          })
          .then(response => {
            this.likeCount = response.data.likeCount
            this.liked = response.data.liked
          })
          .catch(error => {
            alert(error)
          })
        },
        unlike(postId) {
          let url = `/api/posts/${postId}/unlike`

          axios.post(url, {
            user_id: this.userId
          })
          .then(response => {
            this.likeCount = response.data.likeCount
            this.liked = response.data.liked
          })
          .catch(error => {
            alert(error)
          })
        }
      },
    }
</script>
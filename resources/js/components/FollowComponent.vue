<template>
  <div>
    <div class="user-name">
      <p class="name">{{ followuserName }}</p>
      <button v-if="!followed" class="follow-btn" type="button" @click="follow(followuserId)">
        フォローする
      </button>
      <button v-else type="button" class="unfollow-btn" @click="unfollow(followuserId)">
        フォロー中
      </button>
    </div>
    <div class="sub-content">
      <p>{{ postsCount }} posts</p>
      <p><a :href="`/users?user_id=${followuserId}&showfollowers=1`">{{ followers_count }} followers</a></p>
      <p><a :href="`/users?user_id=${followuserId}&showfollowers=0`">{{ following_count }} following</a></p>
    </div>
  </div>
</template>

<script>
    export default {
      props: ['authuserId', 'authuserName', 'followuserId', 'followuserName', 'postsCount', 'defaultFollowed', 'defaultfollowersCount', 'defaultfollowingCount'],
      data() {
        return {
          followed: false,
          followers_count: 0,
          following_count: 0
        };
      },
      created() {
        this.followed = this.defaultFollowed
        this.followers_count = this.defaultfollowersCount
        this.following_count = this.defaultfollowingCount
      },
      methods: {
        follow(followuserId) {
          let url = `/api/users/${followuserId}/follow`

          axios.post(url, {
            authuserId: this.authuserId
          })
          .then(response => {
            this.followers_count = response.data.followers_count
            this.following_count = response.data.following_count
            this.followed = response.data.followed
          })
          .catch(error => {
            alert(error)
          })
        },
        unfollow(followuserId) {
          let url = `/api/users/${followuserId}/unfollow`

          axios.post(url, {
            authuserId: this.authuserId
          })
          .then(response => {
            this.followers_count = response.data.followers_count
            this.following_count = response.data.following_count
            this.followed = response.data.followed
          })
          .catch(error => {
            alert(error)
          })
        },
      },
    }
</script>
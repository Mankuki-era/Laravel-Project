<template>
  <div class="comment-area">
    <div class="comment-box">
      <p class="comment-title"><i class="far fa-comment-dots fa-lg"></i> コメント欄</p>
      <div class="comment-lists">
        <div v-for="(comment, key) in comments" :key="key" class="user-comment">
          <div class="user-info">
            <a :href="`/users/${userId}`"><img :src="comment.profileImgUrl" alt="プロフィール画像"></a>
            <div class="user-secondary">
              <a :href="`/users/${userId}`"><p class="user-name">{{ comment.userName }}</p></a>
              <p class="comment-date">{{ comment.createdAt }}</p>
            </div>
          </div>
          <div class="main-content">
            {{ comment.content }}
          </div>
        </div>
      </div>

      <div class="comment-form">
        <form @submit.prevent="addComment(postId, userId, userName, profileUrl)">
          <div class="form-group">
            <label for="content">コメント</label>
            <div class="form-items">
              <textarea id="content" class="form-control" name="content" rows="3" v-model="newComment" required></textarea>
            </div>
          </div>
          <div class="form-group submit-btn">
            <button type="submit" class="btn btn-primary">
              投稿する
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
      props: ['postId', 'userId', 'userName', 'profileUrl', 'commentsArray'],
      data() {
        return {
          newComment: '',
          comments: []
        }
      },
      created() {
        this.comments = this.commentsArray;
      },
      methods: {
        addComment(postId, userId, userName, profileUrl) {
          let url = `/api/posts/${postId}/comment`

          axios.post(url, {
            user_id: userId,
            user_name: userName,
            profileImg_url: profileUrl,
            content: this.newComment
          })
          .then(response => {
            this.comments.push({
              'createdAt': response.data.created_at,
              'userName': response.data.user_name,
              'profileImgUrl': response.data.profileImg_url,
              'content': response.data.content
            });
            console.log(this.comments);
            this.newComment = '';
          })
          .catch(error => {
            alert(error)
          })
        }
      }
    }
</script>
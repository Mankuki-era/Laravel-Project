### アプリケーション名
　**Recipathy**
 
### アプリケーションの概要
　**このアプリケーションは写真を通して毎日の出来事を伝えあう投稿共有サービスです**   
 
### 工夫した点
- いいねやコメント、フォロー機能にVue.jsを用いることでリアクティブにデータ変更と表示更新を行いました。
- コメント記述を適度にしてコードを見やすくする工夫をしました。
- モバイルファーストを意識したレスポンシブWebデザインで設計しました。

### デモ
　[サイトはこちらから](http://murmuring-headland-13028.herokuapp.com/)
 
### アプリケーションで使っている技術
- インフラ: heroku 
- データベース: MySQL  5.7.26
- 開発環境: MAMP 4.2.0
- 言語: Laravel 7.28.1, Vue.js
- 画像ストレージ: AWS S3  

### アプリケーションの機能
- 認証機能: Authentication
- 投稿のCRUD機能
- 投稿へのコメント機能
- ユーザーのプロフィール編集機能
- 投稿へのいいね機能 
- 画像投稿機能: flysystem-aws-s3-v3(S3パッケージ)
- ハッシュタグによるタグ付け機能: 正規表現
- 投稿、ユーザー検索機能
- ユーザー同士のフォロー機能

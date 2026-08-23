# coachtechフリマ

# 動作確認URL
・Webアプリケーション: http://localhost/
・phpMyAdmin: http://localhost:8080/


##　機能一覧
・ユーザー機能
会員登録/ログイン/ログアウト（認証機能）
プロフィール編集（名前、住所、プロフィール画像）

・商品機能
商品一覧表示/詳細表示
商品出品（画像アップロード、価格、商品説明、状態設定）
カテゴリーの複数紐づけ
商品検索（キーワード検索）

・商品購入・いいねコメント機能
商品購入（支払方法選択、配送先指定）
いいね機能（マイリスト登録）
コメント投稿機能

# 使用技術
・Laravel 8.75
・PHP 8.1
・Mysql 8.0.26
・nginx 1.21.1

# 外部サービス
・stripe（オンライン決済機能）
・mailtrap（メール認証機能）

## 環境構築
1.リポジトリクローン
・HTTP
git clone https://github.com/redameteor/frea-market.git
・SSH
git clone git@github.com:redameteor/frea-market.git

2. .envのコピー、.envの書き換え
cp .env.example .env
Stripe: STRIPE_KEY, STRIPE_SECRET（テスト用パブリックキー・シークレットキー）
Mailtrap: MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD

3.Dockerコンテナの起動
docker-compose up -d --build

4.コンポーザーのインストール
docker-compose exec php composer install

5.アプリケーションキーの生成
docker-compose exec php php artisan key:generate

6.マイグレーションとシーディング
docker-compose exec php php artisan migrate --seed

7.ストレージリンク（画像アップロード用）
docker-compose exec php php artisan storage:link
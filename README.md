# AppStore-backend
這個是放後端(laravel)的git


1. 我們需要進行composer install來解決dependencies
    composer install

2. 建立.env檔案，因為.env預設是github所忽略的檔案
    cp .env.example .env

3. 因為env.example中預設沒有app key，所以我們在.env中生成新的app key
    php artisan key:generate

4. 接下來開啟我們剛複製的.env檔案，將資料庫資訊填入相應的位置
    儲存後，執行
    php artisan migrate
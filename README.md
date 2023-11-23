1. install symfony-cli
2. install dependencies
```
composer install

npm install
```
3. copy .env to .env.local and configure database-url

4. migrate database
```
php bin/console migrate
```

5. start symfony server
```
symfony server:start
```
6. start assert server
```
npm run dev-server
```




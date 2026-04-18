# 起動コマンド
nvm use node
composer run dev
※ tailwindを利用するため、viteが必要なため

# 追加コマンド
php artisan make:model Image -a

php artisan migrate

# フォーマット
## Php
./vendor/bin/pint
## Blade
nvm use node
npx blade-formatter --write resources/views/**/*.blade.php

# playwright
  npx playwright test
    Runs the end-to-end tests.

  npx playwright test --ui
    Starts the interactive UI mode.

  npx playwright test --project=chromium
    Runs the tests only on Desktop Chrome.

  npx playwright test example
    Runs the tests in a specific file.

  npx playwright test --debug
    Runs the tests in debug mode.

  npx playwright codegen
    Auto generate tests with Codegen.

  npx playwright show-report

  npx playwright test --update-snapshots
pipeline {
    agent any

    stages {        
        stage('Prepare Docker and Deploy') {
            steps {
                echo '1. First, build the PHP application image...'
                sh 'sudo docker compose build hv-ap'                    
                
                echo '2. Now build the Nginx image (it can now find laravel-app:latest)...'
                sh 'sudo docker compose build hv-web'

                echo '3. Restarting containers...'
                sh 'sudo docker compose down'
                // --env-fileオプションで環境変数ファイルを指定    
                sh 'sudo docker compose --env-file jenkins.env up -d'
            }
        }
    }

    post {
        // ビルド成功時に実行
        success {
            echo 'Build succeeded! Sending notification...'
            sh """
                curl -X POST -H "Content-Type: application/json" \
                -d '{"content":"✅ ビルド成功: ${env.JOB_NAME} #${env.BUILD_NUMBER}", "channelId":"1"}' \
                http://home-web-nginx/hc/ap/messages/webhook
            """
        }
        
        // ビルド失敗時に実行
        failure {
            echo 'Build failed! Sending notification...'
            sh """
                curl -X POST -H "Content-Type: application/json" \
                -d '{"content":"❌ ビルド失敗: ${env.JOB_NAME} #${env.BUILD_NUMBER}", "channelId":"1"}' \
                http://home-web-nginx/hc/ap/messages/webhook
            """
        }
    }

}
pipeline {
    agent any

    stages {        
        stage('Prepare Docker and Deploy') {
            steps {
                echo 'Building Docker Compose services...'
                sh 'sudo docker compose build'
                    
                echo 'Stopping and removing old containers...'
                sh 'sudo docker compose down'

                // --env-fileオプションで環境変数ファイルを指定    
                echo 'Starting new containers...'
                sh 'sudo docker compose --env-file jenkins.env up -d'
            }
        }
    }
}
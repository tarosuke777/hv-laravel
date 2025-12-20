pipeline {
    agent any

    stages {        
        stage('Prepare Docker and Deploy') { // ステージ名を変更
            steps {
                echo 'Building Docker Compose services...'
                sh 'sudo docker compose build'
                    
                echo 'Stopping and removing old containers...'
                sh 'sudo docker compose down'
                    
                echo 'Starting new containers...'
                sh 'sudo docker compose --env-file jenkins.env up -d'
            }
        }
    }
}
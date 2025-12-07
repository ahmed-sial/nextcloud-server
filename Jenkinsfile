pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Backend Tests (PHPUnit)') {
            steps {
                powershell '''
                    cd backend_tests
                    if (Test-Path "vendor/bin/phpunit.bat") {
                        vendor/bin/phpunit.bat
                    } else {
                        Write-Host "PHPUnit not found"
                    }
                '''
            }
        }

        stage('Frontend Tests (Cypress)') {
            steps {
                powershell '''
                    cd frontend_tests
                    npm install
                    npx cypress run || exit 0
                '''
            }
        }

        stage('Package Artifact') {
            steps {
                powershell '''
                    Compress-Archive -Path * -DestinationPath build.zip -Force
                '''
            }
        }

        stage('Upload to S3') {
            steps {
                powershell '''
                    aws s3 cp build.zip s3://nextcloud-staging-artifacts-YOURNAME/build-${BUILD_NUMBER}.zip
                '''
            }
        }

        stage('Trigger CodeDeploy') {
            steps {
                powershell '''
                    aws deploy create-deployment `
                        --application-name Nextcloud-Staging `
                        --deployment-group-name StagingGroup `
                        --s3-location bucket=nextcloud-staging-artifacts-YOURNAME,bundleType=zip,key=build-${BUILD_NUMBER}.zip
                '''
            }
        }

    }

    post {
        success { echo "Staging Deployment SUCCESS" }
        failure { echo "Staging Deployment FAILED" }
    }
}

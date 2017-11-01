pipeline {
  agent any

  environment {
    APP_NAME = 'amadeus'
    TEAM_NAME = 'search-and-compare'

    REGISTRY = '630542070554.dkr.ecr.eu-central-1.amazonaws.com'
  }

  stages {
    stage('Checkout'){
      steps {
        checkout scm
      }
    }

    stage('Build') {
      environment {
        AWS_COMPOSER_CACHE_S3_BUCKET = 's3://invia-composer-cache'
      }

      steps {
        withCredentials([
          usernamePassword(credentialsId: 'AWS', usernameVariable: 'AWS_ACCESS_KEY_ID', passwordVariable: 'AWS_SECRET_ACCESS_KEY')
        ]) {
          sh './scripts/build.sh'
        }
      }
    }

    stage('Test') {
      steps {
        sh './scripts/test.sh'
      }
    }

    stage('Push') {
      environment {
        AWS_REGION = 'eu-central-1'
      }

      steps {
        withCredentials([
          usernamePassword(credentialsId: 'AWS', usernameVariable: 'AWS_ACCESS_KEY_ID', passwordVariable: 'AWS_SECRET_ACCESS_KEY')
        ]) {
          sh './scripts/push.sh'
        }
      }
    }

    stage('Deploy staging') {
      environment {
        ENVIRONMENT   = 'staging'
        K8S_HOST      = 'https://api.dev.invia.io'
        K8S_NAMESPACE = 'staging'
      }

      steps {
        withCredentials([
          file(credentialsId: 'K8S_STAGING_CA', variable: 'K8S_CA_PATH'),
          usernamePassword(credentialsId: 'K8S_STAGING', usernameVariable: 'K8S_USERNAME', passwordVariable: 'K8S_PASSWORD')
        ]) {
          sh './scripts/deploy.sh'
        }
      }
    }
  }
}

// vi: set ft=groovy :

pipeline {
  agent any

  environment {
    APP_NAME = 'amadeus'
    TEAM_NAME = 'search-and-compare'

    REGISTRY = '630542070554.dkr.ecr.eu-central-1.amazonaws.com'
    KUBETOKEN_HOST = 'https://kube-signin.invia.lan'
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
          usernamePassword(credentialsId: 'AWS', usernameVariable: 'AWS_ACCESS_KEY_ID', passwordVariable: 'AWS_SECRET_ACCESS_KEY'),
          sshUserPrivateKey(credentialsId: 'GIT_PRIVATE_KEY', keyFileVariable: 'GIT_PRIVATE_KEY')
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
        K8S_NAMESPACE = 'search'
      }

      steps {
        withCredentials([
          usernamePassword(credentialsId: 'KUBETOKEN_STAGING', usernameVariable: 'KUBETOKEN_USERNAME', passwordVariable: 'KUBETOKEN_PASSWORD')
        ]) {
          sh './scripts/deploy.sh'
        }
      }
    }

    stage('Deploy production') {
      environment {
        ENVIRONMENT   = 'production'
        K8S_NAMESPACE = 'search'
      }

      steps {
        withCredentials([
          usernamePassword(credentialsId: 'KUBETOKEN_PRODUCTION', usernameVariable: 'KUBETOKEN_USERNAME', passwordVariable: 'KUBETOKEN_PASSWORD')
        ]) {
          sh './scripts/deploy.sh'
        }
      }
    }
  }
}

#!/usr/bin/env bash

# Validate Environment Variable Function
# Validate that each value pass has an environment variable define, else exit with error
validate_env_var() {
    for var in $@
    do
        if [ -z "${!var}" ] ; then
            echo "Variable $var is not set" 1>&2
            exit 1
        fi
    done
}

# Post Github Issue Function
# Post Issue to Github only if the current branch is the main branch
post_github_issue() {

    # Validate that ENV Variables exists
    validate_env_var \
    TRAVIS_BRANCH \
    PROJECT_RELEASE_BRANCH

    # Skip Github issue creation if the branch which the build failed, is not the projects main branch
    if [ $PROJECT_RELEASE_BRANCH != $TRAVIS_BRANCH ]; then
     echo "Skipping creating Github issue, current branch $TRAVIS_BRANCH is not the main branch." 1>&2
     exit 0
    else

        # Validate that ENV Variables exists
        validate_env_var \
            TRAVIS_REPO_SLUG \
            GITHUB_USER_USERNAME \
            GITHUB_USER_PASSWORD \
            TRAVIS_BUILD_NUMBER \
            TRAVIS_BUILD_ID \
            TRAVIS_TAG

        # Make curl POST request to the issue creation endpoint, create a Github issue.
        # Only return the HTTP Status code, ignores the response body.
        # This endpoint returns 201 if successful
        HTTP_STATUS_CODE=$(curl -s -o ./dev/null -w '%{http_code}' -X POST \
            https://api.github.com/repos/$TRAVIS_REPO_SLUG/issues \
            -H "authorization: Basic $(echo -n "$GITHUB_USER_USERNAME:$GITHUB_USER_PASSWORD" | base64)" \
            -H "cache-control: no-cache" \
            -H "content-type: application/json" \
            -d  "{
	            \"title\": \"Travis Deployment #$TRAVIS_BUILD_NUMBER failed for branch:[$TRAVIS_BRANCH]\",
	            \"body\": \"The Travis CI deployment number:**$TRAVIS_BUILD_NUMBER** failed for the branch:**[$TRAVIS_BRANCH]**. \n The build artifact **[$TRAVIS_TAG]** couldn't be promoted. See deployment information [here](https://travis-ci.org/$TRAVIS_REPO_SLUG/builds/$TRAVIS_BUILD_ID)\",
	            \"labels\": [\"help wanted\",\"build failed\"]
            }"
        )

            # Validate that the HTTP Status Code is 201, else print error and exit 1
            if [ $HTTP_STATUS_CODE != "201" ]; then
                echo "Couldn't create github issue, response code was $HTTP_RESPONSE_CODE" 1>&2
                exit 1
            fi
    fi

}

$@

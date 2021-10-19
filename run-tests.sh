bold=$(tput bold)
red=$(tput setaf 1)
green=$(tput setaf 2)
normal=$(tput sgr0)
RESULTS=""
HAS_FAILED_TESTS=0

function run() {
    PHP_VERSION=$1

    echo ""
    echo "--------------------------------------------"
    echo "Testing with PHP ${bold}$PHP_VERSION${normal}"
    echo "--------------------------------------------"

    docker run \
        -it \
        --rm \
        --name php"$PHP_VERSION" \
        -v "$PWD":/usr/src \
        -w /usr/src/ \
        php:"$PHP_VERSION"-cli \
        php ./vendor/bin/phpunit

    if [ $? -eq 0 ]; then
        RESULTS="$RESULTS\n${green}✓ PHP $PHP_VERSION${normal}"
    else
        HAS_FAILED_TESTS=1
        RESULTS="$RESULTS\n${red}𐄂 PHP $PHP_VERSION${normal}"
    fi

    echo "(PHP $PHP_VERSION)"
}

# Get the arguments from the call (i.e. ./run-tests.sh 7.4)
SET_PHP_VERSION=$1

# If an arguments is given, only run that version.
if [ "$#" -eq 1 ]; then
    run "$SET_PHP_VERSION"
else
    # Run tests for different PHP 7 versions.
    for phpversion in {3..4}; do
        run "7.$phpversion"
        RESULTS="$RESULTS\n"
    done
fi

echo ""
echo "RESULTS"
echo "----------------------"
printf "$RESULTS"

exit $HAS_FAILED_TESTS


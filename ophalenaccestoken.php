/**
 * Haalt de access token op, vernieuwt indien nodig.
 */
function getAccessToken() {
    $tokens = getTokens(); // Haalt de opgeslagen tokens op
    $accessToken = $tokens['access_token'] ?? null;

    // Controleer of er een access token is
    if (!$accessToken) {
        $accessToken = refreshAccessToken();
    }

    return $accessToken;
}

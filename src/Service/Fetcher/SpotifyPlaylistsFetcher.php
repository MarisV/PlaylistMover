<?php 

namespace App\Service\Fetcher;

use App\Entity\User;
use App\Entity\UserOAuth;
use Exception;

class SpotifyPlaylistsFetcher extends AbstractPlaylistFetcher 
{   
    private const PROVIDER = 'spotify';

    private CONST FETCH_URL = 'https://api.spotify.com/v1/users/:user_id/playlists';


    final function fetch()
    {   
        /** @var User $user */
        $user = $this->security->getUser();

        $auth = $user->getUserOAuthByProviderKey(self::PROVIDER);

        $url = strtr(self::FETCH_URL, [
            ':user_id' => $auth->getUsername()
        ]);


        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'headers' => [
                       "Authorization: Bearer " . $auth->getAccessToken(),
                    ]
                ]
            );
    
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }


        dd($response->toArray());
        

        return [
            'count' => 10,
            'data' => $response
        ];
    }
}
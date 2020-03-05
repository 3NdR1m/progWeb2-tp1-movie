<?php

namespace Tests\Feature;

use App\Language;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class MovieTest extends TestCase
{

    use RefreshDatabase;

    private $movieParameters = [
        'title' => 'Have it Your Way',
        'release_year' => 2018,
        'length' => 1,
        'description' => 'https://www.youtube.com/watch?v=EFtU3olKhpE',
        'language_id' => 0
    ];

    private function actinAsUser() {
        $user = factory(User::class)->create();
        return $this->actingAs($user);
    }

    private function actinAsAdmin() {
        $user = factory(User::class)->create();
        $user->is_admin = true;
        return $this->actingAs($user);
    }

    /// TEST: [get] /api/movies
    /**
     * description
     * 
     * @test
     * @return void
     */
    public function getIndexAsGuess_returnsAllMoviesPaginated() {

        $response = $this->json('GET', '/api/movies');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
                'links' => [],
                'meta' => []
            ]);
            // ->assertJsonStructure([
            //     'id', 'title', 'release_year', 'length', 'description', 'rating', 'language'
            // ])
    }

    /// TEST: [post] /api/movies
    /**
     * Undocumented function
     *
     * @test
     * @return void
     */
    public function postIndexAsAdminWithRequiredParameter_storeMovieInDatabase() {
        $this->actinAsAdmin()
            ->post('/api/movies', $this->movieParameters)
            ->assertStatus(201);

        $this->assertDatabaseHas('movies', $this->movieParameters);
    }

    /**
     * Undocumented function
     *
     * @test
     * @return void
     */
    public function postIndexAsGuess_returnsStatus401() {
        $this->post('api/movies', $this->movieParameters)
            ->assertStatus(401);
    }

    /**
     * Undocumented function
     *
     * @test
     * @return void
     */
    public function postIndexAsUser_returnsStatus403() {
        $this->actinAsUser()
            ->post('api/movies', $this->movieParameters)
            ->assertStatus(403);
    }

    /**
     * Undocumented function
     * 
     * @test
     * @return void
     */
    public function postIndexAsAdminWithoutRequiredParameter_returnsStatus400() {
        $this->actinAsAdmin()
            ->post('/api/movies')
            ->assertStatus(400);
    }

    /*
    create 
    rating enum
    specialFeature
    fillable
    language
    reviews
    actors
    hasActors
    addActor
    Authentification

    MOVIE
    create movie
        Index (ADD A MOVIE / Query has min_lenght / Query has max_lenght / query has key_Word / Query rating / load movie + reviews)
            public function it_prevent_non_logged_in_users_from_creating_new_articles()
                {
                    $response = $this->get(route('create_new_article'));
                    $response->assertRedirect('login');
                }
        Load reviews ONLY IF USER(with a review for a specific movie / without a review)
        ShowActors (actors in a specific movie movie/actor)
        Update ONLY IF ADMIN(edit title / edit director / edit actors / edit runtime / edit genre / not admin try to modify = ERROR)
        Destroy  ONLY IF ADMIN(Remove a movie)
        
       
    Research movie
         Mots-clés (dans title et description)
         Classification (rating)
         Durée minimale
         Durée maximale
         Tous les critères sont optionnels et si aucun n’est fourni, on doit simplement retourner les 20 premiers films
    */
}

<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Http\Services\UserSearchQueryHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class UserSearchQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected UserSearchQueryHandler $handler;

    private function populateUsersTable(): void
    {
        $user = new User();
        $user->id = 1;
        $user->first_name = "manish";
        $user->last_name = "Sainju";
        $user->user_email = "manish@gmail.com";
        $user->created_at = "2024-05-13T18:25:27.000000Z";
        $user->save();

        $user2 = new User();
        $user->id = 2;
        $user2->first_name = "Alex";
        $user2->last_name = "Turner";
        $user2->user_email = "alex@gmail.com";
        $user->created_at = "2024-06-13T18:25:27.000000Z";
        $user2->save();
    }
    protected function setUp(): void
    {
        parent::setUp();
        $this->populateUsersTable();
        //User::factory(10)->create();
        //
        $this->handler = new UserSearchQueryHandler(new User());
    }

    public function testBuildQueryShouldReturnInstanceOfBuilderClass()
    {
        // Mocking the request
        $request = Request::create('/', 'GET', [
            'search_col' => 'fname',
            'search_col_val' => 'John',
            'sort' => 'fname_asc'
        ]);

        $builder = $this->handler->buildQuery($request);

        $this->assertInstanceOf(Builder::class, $builder);
        // Add assertions for the expected query clauses based on the provided request parameters
    }


    public function testBuildSearchQueryRequest()
    {
        // Mocking request object
        $request = Request::create('/', 'GET', [
            'search_col' => 'fname',
            'search_col_val' => 'manish'
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);

        $this->assertCount(1, $builder->get()->toArray());
    }

    public function testShouldReturnEmptyArray(): void
    {
        $request = Request::create('/', 'GET', [
            'search_col' => 'fname',
            'search_col_val' => 'xyz'
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);

        $this->assertCount(0, $builder->get()->toArray());
    }

    public function testShouldSortDesc(): void
    {
        $request = Request::create('/', 'GET', [
            'sort' => 'fname_desc',
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);
        $result = $builder->get()->toArray();
        $this->assertSame('manish', $result[0]['first_name']);
    }

    public function testShouldSortAsc(): void
    {
        $request = Request::create('/', 'GET', [
            'sort' => 'fname_asc',
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);
        $result = $builder->get()->toArray();
        $this->assertSame('Alex', $result[0]['first_name']);
    }

    public function testShouldQuickSearch(): void
    {
        $request = Request::create('/', 'GET', [
            'search' => 'manish',
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);
        $result = $builder->get()->toArray();
        $this->assertSame('manish', $result[0]['first_name']);
    }

    public function testShouldSearchByColumn(): void
    {
        $request = Request::create('/', 'GET', [
            'search_col' => 'fname',
            'search_col_val' => 'alex',
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);
        $result = $builder->get()->toArray();
        $this->assertSame('Alex', $result[0]['first_name']);
    }

    public function testShouldSortByColumnFirstIfQuickSearchIsPresent(): void
    {
        $request = Request::create('/', 'GET', [
            'search' => 'manish',
            'search_col' => 'fname',
            'search_col_val' => 'alex',
        ]);

        $usersQuery = User::query();
        $builder = $this->handler->buildQuery($request, $usersQuery);
        $result = $builder->get()->toArray();
        $this->assertSame('Alex', $result[0]['first_name']);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;

class testAccount extends TestCase {

    /**
     * A basic feature test example.
     *
     * @return void
     */
    use WithFaker,
        RefreshDatabase;
    
    /** @test */
    public function a_guest_cant_create_an_account() {
        $attrs = [
            'name' => $this->faker->name,
            'access_key_id' => $this->faker->password,
            'access_key' => $this->faker->password,
            'workspaces' => $this->faker->sentence,
        ];
        
        $response = $this->post('/accounts', $attrs)->assertRedirect('login')->assertStatus(302);
        
    }

    /** @test */
    public function can_be_created() {
        $this->withoutExceptionHandling();
        $attrs = [
            'name' => $this->faker->name,
            'access_key_id' => $this->faker->password,
            'access_key' => $this->faker->password,
            'workspaces' => $this->faker->sentence,
        ];
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/accounts', $attrs)->assertRedirect('accounts');
        $this->assertDatabaseHas('accounts', $attrs);
        $this->get('/accounts')->assertSee($attrs['name'])->assertViewIs('accounts')->assertViewHasAll(['accounts' => Account::all(), 'account' => []]);
    }
    
    /** @test */
    public function required_fields_are_rquired() {
        $attrs = [
            'name' => '',
            'access_key_id' => '',
            'access_key' => '',
        ];
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/accounts', $attrs);
        $response->assertSessionHasErrors(['name','access_key_id','access_key']); 
    }
    
    /** @test */
    public function unique_fields_are_unique_required() {
        $attrs = [
            'name' => 'name',
            'access_key_id' => 'access_key_id',
            'access_key' => 'access_key',
        ];
        $user = User::factory()->create();
        $this->actingAs($user)->post('/accounts', $attrs);
        $response = $this->actingAs($user)->post('/accounts', $attrs);
        $response->assertSessionHasErrors(['name'=>'The name has already been taken.','access_key_id'=>'The access key id has already been taken.','access_key'=>'The access key has already been taken.']); 
    }
    
    /** @test */
    public function can_be_deleted() {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create();
        $account = Account::factory()->create();
        $response = $this->actingAs($user)
                ->delete($account->path())
                ->assertRedirect('accounts');
        $this->assertCount(0,Account::all());  
    }
    
    /** @test */
    public function can_be_updated() {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create();
        $account = Account::factory()->create();
        
        $attrs = [
            'name' => 'updated',
            'access_key_id' => $this->faker->password,
            'access_key' => $this->faker->password,
            'workspaces' => $this->faker->sentence,
        ];
        
        $response = $this->actingAs($user)
                ->put('/accounts/'.$account->id, $attrs)
                ->assertRedirect('accounts');
        $this->assertCount(1,Account::all());
        $this->assertDatabaseHas('accounts', $attrs);
    }

}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_store_todo_with_due_date()
    {
        $response = $this->actingAs($this->user)
            ->post(route('todos.store'), [
                'task' => 'Test task',
                'due_date' => '2025-04-03T10:00'
            ]);

        $response->assertRedirect(route('todos.index'));
        $this->assertDatabaseHas('todos', [
            'task' => 'Test task',
            'due_date' => '2025-04-03T10:00',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_toggle_todo_completion()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('todos.update', $todo));

        $response->assertRedirect(route('todos.index'));
        $this->assertEquals(1, $todo->fresh()->completed);
    }


    /** @test */
    public function it_validates_task_required()
    {
        $response = $this->actingAs($this->user)
            ->post(route('todos.store'), [
                'due_date' => '2025-04-03T10:00'
            ]);

        $response->assertSessionHasErrors('task');
    }

    /** @test */
    public function it_validates_due_date_format()
    {
        $response = $this->actingAs($this->user)
            ->post(route('todos.store'), [
                'task' => 'Test task',
                'due_date' => 'invalid-date'
            ]);

        $response->assertSessionHasErrors('due_date');
    }

    /** @test */
    public function it_allows_editing_incomplete_todos()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('todos.edit', $todo));
        $response->assertOk();
    }

    /** @test */
    public function it_prevents_editing_completed_todos()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'completed' => true
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('todos.edit', $todo));
        $response->assertForbidden();
    }

    /** @test */
    public function it_prevents_toggling_other_users_todos()
    {
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('todos.toggle', $todo));

        $response->assertForbidden();
    }

    /** @test */
    public function it_can_update_task_and_due_date()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'task' => 'Old task',
            'due_date' => '2025-01-01 00:00:00',
            'completed' => false
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('todos.updateTask', $todo), [
                'task' => 'Updated task',
                'due_date' => '2025-12-31T23:59'
            ]);

        $response->assertRedirect(route('todos.index'));
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'task' => 'Updated task',
            'due_date' => '2025-12-31T23:59',
            'completed' => false,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_prevents_updating_other_users_todos()
    {
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $otherUser->id,
            'task' => 'Old task',
            'completed' => false
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('todos.updateTask', $todo), [
                'task' => 'Updated task'
            ]);

        $response->assertForbidden();
    }
}

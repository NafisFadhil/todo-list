<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_a_todo_with_due_date()
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'task' => 'Test task',
            'due_date' => '2025-04-03 10:00:00',
            'completed' => false
        ]);

        $this->assertDatabaseHas('todos', [
            'user_id' => $this->user->id,
            'task' => 'Test task',
            'due_date' => '2025-04-03 10:00:00',
            'completed' => false
        ]);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(User::class, $todo->user);
        $this->assertEquals($this->user->id, $todo->user->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['task', 'completed', 'due_date', 'user_id'];
        $todo = new Todo();
        
        $this->assertEqualsCanonicalizing($fillable, $todo->getFillable());
    }

    /** @test */
    public function it_casts_completed_to_boolean()
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'task' => 'Test task',
            'completed' => 1,
            'due_date' => null
        ]);

        $this->assertTrue((bool)$todo->completed);
    }

    /** @test */
    public function it_can_toggle_completion_status()
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'task' => 'Test task',
            'completed' => false,
            'due_date' => null
        ]);

        $todo->update(['completed' => true]);
        $this->assertTrue((bool)$todo->fresh()->completed);
    }

    /** @test */
    public function due_date_is_nullable()
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'task' => 'Task without due date',
            'completed' => false
        ]);

        $this->assertNull($todo->due_date);
    }

    /** @test */
    public function it_requires_task_field()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Todo::create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);
    }

    /** @test */
    public function it_requires_user_id_field()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Todo::create([
            'task' => 'Test task',
            'completed' => false
        ]);
    }
}

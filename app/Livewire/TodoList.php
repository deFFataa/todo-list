<?php

namespace App\Livewire;

use Livewire\Attributes\Rule;
use Livewire\Component;

use App\Models\Todo;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;

    #[Rule(['required', 'min:3'])]
    public $name;

    public $search;

    public $EditTodoID;

    #[Rule(['required', 'min:3'])]
    public $EditTodoName;

    public function create(){
        $validate = $this->validateOnly('name');    

        Todo::create($validate);

        $this->reset('name');

        session()->flash('success', 'Created');
    }

    public function toggle(Todo $todo){
        $todo->completed = !$todo->completed;

        $todo->save();
    }

    public function destroy($todoID){

        try {
            Todo::findOrFail($todoID)->delete();
        } catch (\Exception $e) {
            session()->flash('error', 'There was an error deleting this list.');
            return;
        }
    }

    public function edit($todoID){
        $this->EditTodoID = $todoID;
        $this->EditTodoName = Todo::find($todoID)->name;
    }

    public function cancel(){
        $this->reset('EditTodoID', 'EditTodoName');
    }

    public function update(){
        Todo::find($this->EditTodoID)->update([
            'name' => $this->EditTodoName
        ]);

        $this->cancel();
    }

    public function render()
    {
        $todos = Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5);
        return view('livewire.todo-list', ['todo' => $todos]);
    }
}

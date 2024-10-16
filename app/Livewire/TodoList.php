<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;
    protected $rules = [
        'name' => 'required|min:3|max:50',
    ];

    public $name; 
    public $search;

    protected $Editngrules = [
        'name' => 'required|min:3|max:50',
    ];
    public $editingtodoID;
    public $editingTodoName;

    public function create(){
    //    validate
        $validated = $this->validateOnly('name');

    // create the todo
        Todo::create($validated);

    // clear the input 
        $this->reset('name');  
    // send flash message
       session()->flash('success','Created');
       $this->resetPage();
    }

    public function delete(Todo $todoID)
    {
        try {
            $todoID->delete();
            session()->flash('success', 'Todo deleted successfully!');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete todo!');
            return;
        }
    
        // Optionally, emit an event to the frontend or handle post-delete logic
    }
    

    public function toggle($todoID){
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save(); 
    }

    public function edit($todoID){
        $this->editingtodoID =  $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    public function cancelEdit(){
        $this->reset('editingtodoID', 'editingTodoName');
    }
    
    // public function update(){
    //     $this->validateOnly('editingTodoName');
    //     Todo::find($this->editingTodoID)->update(
    //         [
    //             'name' => $this->editingTodoName
    //         ]
    //     );
    //     $this->cancelEdit();
    // }
    public function update() {
        $this->validate([
            'editingTodoName' => 'required|min:3|max:50',
        ]);
        
        Todo::find($this->editingtodoID)->update([
            'name' => $this->editingTodoName
        ]);
    
        $this->cancelEdit();
    }
    public function render()
    {
        
        return view('livewire.todo-list',[
            // 'todos' => Todo::latest()->get()
            // 'todos' => Todo::latest()->paginate(5)
            'todos' => Todo::latest()->where('name','like',"%{$this->search}%")->paginate(5)
        ]);
    }
}

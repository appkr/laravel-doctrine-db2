<?php

use App\Domain\Entities\Task;
use App\Domain\Repositories\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;

/**
 * Testing Model Collection (READ)
 */
Route::get('/', function (TaskRepository $repository) {
    return view('tasks', [
        'tasks' => $repository->all('createdAt', 'ASC')
    ]);

    /*
    dd($tasks);
    array:2 [▼
      0 => Task {#269 ▼
            #id: 1
            #name: "First Task"
            #createdAt: DateTime {#265 ▶}
            #updatedAt: DateTime {#266 ▶}
        }
      1 => Task {#273 ▼
            #id: 2
            #name: "Second Task"
            #createdAt: DateTime {#271 ▶}
            #updatedAt: DateTime {#272 ▶}
        }
    ]
    */
});

/**
 * Testing Model Instance (READ)
 */
Route::get('/task/{id}', function ($id, TaskRepository $repository) {
    $task = $repository->find($id);

    return response()->json($task->toArray(), 200, [], JSON_PRETTY_PRINT);
});

/**
 * Testing Model Update & Persistence (UPDATE)
 */
Route::get('/task/{id}/update', function ($id, TaskRepository $repository, EntityManagerInterface $em) {
    $task = $repository->find($id);
    $task->setName('Modified ' . $task->getName());
    $em->persist($task);
    $em->flush();

    return response()->json($task->toArray(), 200, [], JSON_PRETTY_PRINT);
});

/**
 * Testing Model Creating & Persistence (CREATE)
 */
Route::post('/task', function (Request $request, EntityManagerInterface $em) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|max:255',
    ]);

    if ($validator->fails()) {
        return redirect('/')
            ->withInput()
            ->withErrors($validator);
    }

    $task = new Task(
        $request->get('name')
    );

    $em->persist($task);
    $em->flush();

    return redirect('/');
});

/**
 * Testing Model Deletion & Persistence (DELETE)
 */
Route::delete('/task/{id}', function ($id, TaskRepository $repository, EntityManagerInterface $em) {
    $task = $repository->find($id);

    $em->remove($task);
    $em->flush();

    return redirect('/');
});

/**
 * Testing Laravel-Doctrine provided example
 */
Route::get('example', function () {
    $scientist = new App\Domain\Entities\Scientist(
        'Albert',
        'Einstein'
    );

    $scientist->addTheory(
        new App\Domain\Entities\Theory('Theory of relativity')
    );

    EntityManager::persist($scientist);
    EntityManager::flush();

    return response()->json($scientist->toArray(), 200, [], JSON_PRETTY_PRINT);
});

Route::auth();

Route::get('/home', 'HomeController@index');

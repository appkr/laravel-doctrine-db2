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
Route::get('/task/{id}/update', ['middleware' => 'auth', function ($id, TaskRepository $repository, EntityManagerInterface $em) {
    $task = $repository->find($id);

    // Testing Authoriztion
    if (Gate::denies('update', $task)) {
        abort(403);
    }

    $task->setName('Modified ' . $task->getName());
    $em->persist($task);
    $em->flush();

    return response()->json($task->toArray(), 200, [], JSON_PRETTY_PRINT);
}]);

/**
 * Testing Model Creating & Persistence (CREATE)
 */
Route::post('/task', ['middleware' => 'auth', function (Request $request, EntityManagerInterface $em) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|max:255',
    ]);

    if ($validator->fails()) {
        return redirect('/')->withInput()->withErrors($validator);
    }

    $task = new Task($request->get('name'));
    $task->setUser(auth()->user());

    $em->persist($task);
    $em->flush();

    return redirect('/');
}]);

/**
 * Testing Model Deletion & Persistence (DELETE)
 */
Route::delete('/task/{id}', ['middleware' => 'auth', function ($id, TaskRepository $repository, EntityManagerInterface $em) {
    $task = $repository->find($id);

    // Testing Authoriztion
    if (Gate::denies('delete', $task)) {
        abort(403);
    }

    $em->remove($task);
    $em->flush();

    return redirect('/');
}]);

Route::auth();

Route::get('/home', 'HomeController@index');

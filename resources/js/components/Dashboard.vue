<template>
    <div class="card">

        <modal name="add-task-modal" :adaptive="true" :height="400">
            <div class="col-sm py-4">
                <form @submit.prevent="addTask">
                    <h2 class="text-center">Tasks</h2>
                    <div class="form-group">
                        <label for="task-name">
                            <span class="h5">Board Name</span>
                        </label>
                        <select class="custom-select" v-model="task.board_id">
                            <option value disabled>Select a Board</option>
                            <option
                                v-for="board in boards"
                                v-bind:key="board.id"
                                :value="board.id"
                            >{{ board.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="task-name">
                            <span class="h5">Task Name</span>
                        </label>
                        <input type="text" class="form-control" v-model="task.name" />
                    </div>
                    <div class="form-group">
                        <label for="task-decription">
                            <span class="h5">Task Description</span>
                        </label>
                        <textarea class="form-control" v-model="task.description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="h5">Save</span>
                    </button>
                </form>
            </div>
        </modal>

        <modal name="add-board-modal">
            <div class="col-sm py-4">
                <form @submit.prevent="addBoard">
                    <h2 class="text-center">Board</h2>
                    <div class="form-group">
                        <label for="board-name">
                            <span class="h5">Board Name</span>
                        </label>
                        <input type="text" class="form-control" v-model="board.name" />
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="h5">Save</span>
                    </button>
                </form>
            </div>
        </modal>

        <div class="card-body">
            <div class="py-4">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="text-center">Tasks To-Do</h2>
                        <div class="add-flex-center">
                            <button @click="showAddBoard" class="btn btn-success mr-4">
                                <span class="h5">Add Column</span>
                            </button>
                            <button @click="showAddTask" class="btn btn-primary mr-4">
                                <span class="h5">Add Card</span>
                            </button>
                        </div>
                        <main class="flexbox py-4">
                            <div class="row">
                                <div
                                    class="col text-center"
                                    v-for="board in boards"
                                    v-bind:key="board.id"
                                >

                                    <h3 class="alert alert-dark">
                                        {{ board.name }}
                                        <button @click="deleteBoard(board.id,task.id)" class="btn btn-danger">
                                            <span class="h5">x</span>
                                        </button>
                                    </h3>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col" v-for="board in boards" v-bind:key="board.id">
                                    <Board :id="board.id">
                                        <div v-for="task in tasks" v-bind:key="task.id">
                                            <div v-if="board.id == task.board_id">
                                                <Task :id="task.id" draggable="true">
                                                    <h4>{{ task.name }}</h4>
                                                    <p>{{ task.description }}</p>
                                                    <div class="row">
                                                        <div class="col-xl-12 py-1">
                                                            <button
                                                                @click="editTask(task)"
                                                                class="btn btn-warning btn-sm btn-block"
                                                            >
                                                                <span class="h6">Edit</span>
                                                            </button>
                                                        </div>
                                                        <div class="col-xl-12 py-1">
                                                            <button
                                                                @click="deleteTask(task.id)"
                                                                class="btn btn-danger btn-sm btn-block"
                                                            >
                                                                <span class="h6">Delete</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </Task>
                                            </div>
                                        </div>
                                    </Board>
                                    <div class="row py-2">
                                        <div class="col-xl-12 py-2">
                                            <button
                                                @click="editBoard(board)"
                                                class="btn btn-warning btn-block"
                                            >
                                                <span class="h5">Edit</span>
                                            </button>
                                        </div>
                                        <div class="col-xl-12 py-2">
                                            <button
                                                @click="deleteBoard(board.id)"
                                                class="btn btn-danger btn-block"
                                            >
                                                <span class="h5">Delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

Vue.prototype.$user_id = document
    .querySelector("meta[name='user-id']")
    .getAttribute("content");

import vmodal from 'vue-js-modal';
Vue.use(vmodal); 

export default {
    data() {
        return {
            boards: [],
            board: {
                id: "",
                name: ""
            },
            board_id: "",

            tasks: [],
            task: {
                id: "",
                board_id: "",
                name: "",
                description: ""
            },
            task_id: "",
            edit: false
        };
    },
    created() {

        this.fetchBoards();

        this.fetchTasks();
    },
    methods: {

        showAddBoard () {
            this.$modal.show('add-board-modal');
        },

        showAddTask () {
            this.$modal.show('add-task-modal');
        },

        fetchBoards() {
            fetch("api/boards/" + this.$user_id)
                .then(res => res.json())
                .then(res => {
                    this.boards = res.data;
                });
        },
        deleteBoard(id) {
            if (confirm("Are you sure?")) {
                fetch("api/board/" + id + "/" + this.$user_id, {
                    method: "delete"
                })
                    .then(res => res.json())
                    .then(data => {
                        this.fetchBoards();
                    })
                    .catch(error => console.log(error));
            }
        },
        addBoard() {
            this.$modal.hide('add-board-modal');

            if (this.edit === false) {
                fetch("api/board/" + this.$user_id, {
                    method: "post",
                    body: JSON.stringify(this.board),
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.board.name = "";
                        this.fetchBoards();
                    })
                    .catch(error => console.log(error));
            } else {
                fetch("api/board/" + this.$user_id, {
                    method: "put",
                    body: JSON.stringify(this.board),
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.board.name = "";
                        alert("Board Updated. Wait for page reload");
                        window.location.reload();
                    })
                    .catch(error => console.log(error));
            }
        },
        editBoard(board) {
            this.edit = true;
            this.board.id = board.id;
            this.board.board_id = board.id;
            this.board.name = board.name;
            alert("You can edit this in the Board section above.");
            this.$modal.show('add-board-modal');
        },

        //  task data handling

        fetchTasks() {
            fetch("api/tasks/" + this.$user_id)
                .then(res => res.json())
                .then(res => {
                    this.tasks = res.data;
                });
        },
        deleteTask(id) {
            if (confirm("Are you sure?")) {
                fetch("api/task/" + id + "/" + this.$user_id, {
                    method: "delete"
                })
                    .then(res => res.json())
                    .then(data => {
                        alert("Task Deleted. Wait for page reload");
                        window.location.reload();
                        this.fetchTasks();
                    })
                    .catch(error => console.log(error));
            }
        },
        addTask() {
            this.$modal.hide('add-task-modal');

            if (this.edit === false) {
                fetch("api/task/" + this.$user_id, {
                    method: "post",
                    body: JSON.stringify(this.task),
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.task.board_id = "";
                        this.task.name = "";
                        this.task.description = "";
                        alert("Task added. Wait for page reload");
                        window.location.reload();
                    })
                    .catch(error => console.log(error));
            } else {
                fetch("api/task/" + this.$user_id, {
                    method: "put",
                    body: JSON.stringify(this.task),
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        this.task.board_id = "";
                        this.task.name = "";
                        this.task.description = "";
                        alert("Task Updated. Wait for page reload");
                        window.location.reload();
                    })
                    .catch(error => console.log(error));
            }
        },
        editTask(task) {
            this.edit = true;
            this.task.id = task.id;
            this.task.task_id = task.id;
            this.task.board_id = task.board_id;
            this.task.name = task.name;
            this.task.description = task.description;
            alert("You can edit this in the Task section above.");
            this.$modal.show('add-task-modal');
        }
    }
};
</script>

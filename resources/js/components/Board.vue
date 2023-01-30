<template>
    <div :id="id" class="board" @dragover.prevent @drop.prevent="drop">
        <slot />
    </div>
</template>

<script>
export default {
    props: ["id"],
    methods: {
        drop: e => {

            const user_id = document
                .querySelector("meta[name='user-id']")
                .getAttribute("content");


            const task_id = e.dataTransfer.getData("task_id");

            const task = document.getElementById(task_id);

            task.style.display = "block";

            e.target.appendChild(task);

            var task_data = "";
            var board_data = "";


            fetch(
                "api/task/" +
                    e.target.children[e.target.children.length - 1].id +
                    "/" +
                    user_id
            )
                .then(res => res.json())
                .then(res => {
                    task_data = res.data;


                    fetch("api/board/" + e.target.id + "/" + user_id)
                        .then(res => res.json())
                        .then(res => {
                            board_data = res.data;


                            fetch("api/task/" + user_id, {
                                method: "put",
                                body: JSON.stringify({
                                    task_id:
                                        e.target.children[
                                            e.target.children.length - 1
                                        ].id,
                                    user_id: board_data.user_id,
                                    board_id: board_data.id,
                                    name: task_data.name,
                                    description: task_data.description
                                }),
                                headers: {
                                    "Content-Type": "application/json"
                                }
                            })
                                .then(res => res.json())
                                .catch(err => console.log(err));
                        });
                });
        }
    }
};
</script>

<template>

    <v-card class="mt-4 rounded bg-green-lighten-5" flat>
        <v-card-title class="text-green font-weight-light border-b">
            <v-row dense>
                <v-col cols="9">
                    Project Tasks
                </v-col>
                <v-col col="3">
                    <v-btn color="green" flat rounded
                           @click="showNewDialogModal=true;taskName=''; taskDescription=''; editting=false;">
                        <v-icon>mdi-plus</v-icon>
                        Task
                    </v-btn>
                </v-col>
            </v-row>
        </v-card-title>
        <v-card-text style="overflow: auto !important; height: 400px">

            <no-records-component title="No tasks in this project"
                                  description="You have no tasks in this project, click the + task button to create task"
                                  class="ma-2" v-if="!tasks.length"></no-records-component>

            <draggable :list="tasks" @change="graged">

                <v-list-item v-for="item in tasks" v-if="tasks" :key="item.id" class="ma-1 bg-white pa-3 rounded">

                    <template v-slot:prepend>
                         <span class="pa-3 mr-2 text-green text-center"
                               style="border-radius: 35px!important;">
                       {{ item.priority }}
                        <small class="text-red d-block">Priority</small>
                   </span>
                    </template>

                    <v-list-item-title :class="item.done ? 'done':''" class="mb-2 font-weight-bold">{{ item.name }}
                    </v-list-item-title>
                    <v-list-item-subtitle v-if="item.description" :class="item.done ? 'done':''">{{
                            item.description
                        }}
                    </v-list-item-subtitle>
                    <v-list-item-subtitle v-if="item.description" :class="item.done ? 'done':''">
                        Created AT:
                        {{
                            formatDate(item.created_at)
                        }}
                    </v-list-item-subtitle>

                    <v-list-item-subtitle class="mt-2">
                        <v-btn
                            color="green"
                            rounded size="small" variant="text"
                            @click="selectedTask=item;taskName=item.name;taskDescription=item.description;showNewDialogModal=true;editting=true;">
                            Edit
                        </v-btn>
                        <v-btn color="red" rounded size="small" variant="text"
                               @click="selectedTask=item;showDeleteDialog=true"
                        >Delete
                        </v-btn>

                    </v-list-item-subtitle>

                    <template v-slot:append>
                        <v-switch v-model="item.done" class="mt-4 mt-2" color="success" flat inset theme="light"
                                  @change="markTask(item)"></v-switch>
                        <v-btn icon size="large" variant="text">
                            <v-icon>mdi-menu</v-icon>
                        </v-btn>
                    </template>
                </v-list-item>
            </draggable>


        </v-card-text>

    </v-card>

    <v-dialog v-model="showDeleteDialog" :persistent="deletingTask" width="400">
        <v-card>
            <v-card-title>
                Confirm delete
            </v-card-title>
            <v-card-text>
                <h3>Do you want to delete this task?</h3>
            </v-card-text>
            <v-card-actions>
                <v-btn :loading="deletingTask" block color="red" rounded size="large" variant="flat"
                       @click="deleteTask">Yes delete
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="showNewDialogModal" width="400">
        <v-card>
            <v-card-title>
                <v-row dense>
                    <v-col cols="11">
                        {{ editting ? "Update" : " Create new" }} task
                    </v-col>
                    <v-col cols="1">
                        <v-btn color="red" flat icon variant="text" @click="showNewDialogModal=false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-title>
            <v-card-text>
                <v-form ref="taskForm">
                    <v-text-field
                        v-model="taskName"
                        :rules="[required]"
                        class="mt-3"
                        label="Task name"
                        validate-on="blur"
                        variant="outlined"
                    ></v-text-field>

                    <v-textarea
                        v-model="taskDescription"
                        auto-grow
                        class="mt-3"
                        label="Task description"
                        rows="3"
                        variant="outlined"></v-textarea>

                </v-form>

            </v-card-text>
            <v-card-actions>
                <v-btn :loading="savingTask" block color="green" rounded size="large" variant="flat" @click="saveTask">
                    {{ editting ? "Update" : " Save" }} task
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-snackbar v-model="success" color="green" dark>{{ success_message }}</v-snackbar>
    <v-snackbar v-model="error" color="error" dark>{{ error_message }}</v-snackbar>

</template>

<script>

import {VueDraggableNext} from 'vue-draggable-next'
import NoRecordsComponent from "./NoRecordsComponent";

export default {
    name: "TasksComponent",
    components: {
        NoRecordsComponent,
        draggable: VueDraggableNext,
    },
    props: {
        project_id: {
            type: Number,
        }
    },
    data() {
        return {
            tasks: [],
            loadingTasks: false,
            showNewDialogModal: false,
            savingTask: false,
            taskName: "",
            taskDescription: "",
            success: false,
            success_message: "",
            error: false,
            error_message: "",
            selectedTask: null,
            showDeleteDialog: false,
            deletingTask: false,
            editting: false
        }
    },
    watch:{
        project_id(){
            this.getTasks();
        }
    },
    methods: {

        formatDate(date) {
            const newDate = new Date(date);

            return newDate.toJSON();

        },

        graged(task) {

            const newIndex = task.moved.newIndex;
            const oldIndex = task.moved.oldIndex;
            const element = task.moved.element;

            const newPriority = newIndex + 1;

            //update the target task in the backend
            this.changePriority(element.id, newPriority);


            //grab the following item and changed it's priority too

            const followingTask = this.tasks[newPriority];

            const followingPriority = newIndex > oldIndex ? newPriority - 1 : newPriority + 1;

            this.changePriority(followingTask.id, followingPriority);


        },

        deleteTask() {

            this.deletingTask = true;
            const taskID = this.selectedTask.id;
            axios.delete("/api/tasks/" + taskID)
                .then(response => {
                    this.showDeleteDialog = false;
                    this.success = true;
                    this.success_message = "Task deleted";
                    this.getTasks();
                    this.deletingTask = false;

                })
                .catch(error => {
                    this.deletingTask = false;

                })

        },

        changePriority(taskID, newPriority) {
            axios.post("/api/tasks/" + taskID + "/change-priority", {
                priority: newPriority
            }).then(response => {
                this.getTasks();

            });


        },
        markTask(task) {

            if (task && task.done) {
                axios.get("/api/tasks/" + task.id + "/done")
                    .then(res => {

                    })

            } else {


                axios.get("/api/tasks/" + task.id + "/undone")
                    .then(res => {

                    })

            }

        },
        required(v) {
            return !!v || 'Project name is required'
        },
        saveTask() {

            if (this.$refs.taskForm.validate()) {

                this.savingTask = true;

                const data = {
                    name: this.taskName,
                    description: this.taskDescription,
                    project_id: this.project_id
                };

                const url = this.editting ? "/api/tasks/" + this.selectedTask.id : "/api/tasks/";

                axios.post(url, data)
                    .then(response => {
                        this.savingTask = false;
                        if (this.editting) {
                            this.getTasks();
                        } else {
                            this.tasks.unshift(response.data.data);
                        }
                        this.success = true;
                        this.success_message = this.editting ? "Task updated" : "Task saved";
                        this.showNewDialogModal = false;
                    })
                    .catch(error => {
                        this.savingTask = false;
                    })


            }

        },
        getTasks() {
            this.loadingTasks = true;
            const url = "/api/tasks/" + this.project_id;

            axios.get(url)
                .then(response => {
                    this.tasks = response.data.data;
                    this.loadingTasks = false;
                })
                .catch(error => {
                    this.loadingTasks = false;
                });

        }
    },
    mounted() {
        this.getTasks();

    }

}
</script>

<style>

.done {
    text-decoration: line-through
}
</style>

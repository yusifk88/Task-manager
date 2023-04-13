<template>
    <v-row>
        <v-col class="mx-auto" cols="12" sm="6">
            <h1 class="font-weight-light ma-4 text-center display-2">Simple project manager</h1>
            <v-card flat rounded style="height: 85vh; border-radius: 15px">
                <v-card-title class="border-b">
                    <v-autocomplete
                        v-model="selectedProject"
                        :items="projects"
                        :loading="loadingProjects"
                        class="mt-3"
                        color="green"
                        item-title="name"
                        item-value="id"
                        label="Select Project"
                        return-object
                        variant="outlined"
                    >
                        <template v-slot:prepend-item>
                            <v-btn block
                                   color="green"
                                   rounded
                                   size="large"
                                   variant="text"
                                   @click="showCreateProjectModal=true; projectDescription='';projectName='';editting=false;">
                                Create new project
                            </v-btn>
                        </template>
                        <template v-slot:no-data>
                            <p class="text-center text-disabled ma-4 w-100">
                                You do not have any projects, <br>click the create new project button to create your
                                first project
                            </p>
                        </template>
                    </v-autocomplete>
                </v-card-title>

                <v-card-text>

                    <no-records-component
                        v-if="!selectedProject"
                        description="You have not selected a project, select a project to view or add tasks"
                        title="No project selected"
                    ></no-records-component>

                    <v-card v-if="selectedProject" class="bg-green-darken-3 rounded mt-2" flat>
                        <v-card-title class="border-b">
                            <h3 class="font-weight-light">Project details</h3>
                            <v-btn
                                class="mt-3"
                                color="light" size="small" variant="outlined"
                                @click="projectName = selectedProject.name; projectDescription = selectedProject.description;editting=true;showCreateProjectModal=true;">
                                <v-icon>mdi-pencil</v-icon>
                                Edit
                            </v-btn>
                            <v-btn class="mt-3 ml-2" color="light" size="small" variant="outlined"
                                   @click="showDeleteProject=true">
                                <v-icon>mdi-delete-outline</v-icon>
                                Delete
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <h2>{{ selectedProject.name }}</h2>
                            <small>Project Name</small>

                            <span v-if="selectedProject.description">
                               <br> <strong class="font-weight-bold">Description:</strong>
                                <p>{{ selectedProject.description }}</p>

                            </span>

                        </v-card-text>
                    </v-card>

                    <tasks-component v-if="selectedProject" :project_id="selectedProject.id"></tasks-component>


                </v-card-text>
            </v-card>

        </v-col>
    </v-row>


    <v-dialog v-model="showDeleteProject" width="400">
        <v-card>
            <v-card-title>Confirm delete</v-card-title>
            <v-card-text>
                <h4 class="font-weight-light">DO you want to delete this project?</h4>
            </v-card-text>
            <v-card-actions>
                <v-btn :loading="loadingProjects" block color="red" dark rounded size="large" variant="flat"
                       @click="deleteProject">Yes,
                    delete
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="showCreateProjectModal" width="500">
        <v-card>
            <v-card-title>
                <v-row dense>
                    <v-col cols="11">
                        {{ editting ? "Update" : "Create a new" }} project

                    </v-col>
                    <v-col cols="1">
                        <v-btn class="align-self-end" color="red" flat icon variant="text"
                               @click="showCreateProjectModal=false">
                            <v-icon icon="mdi-close"></v-icon>
                        </v-btn>
                    </v-col>


                </v-row>
            </v-card-title>
            <v-card-text>
                <v-form ref="projectForm">
                    <v-text-field v-model="projectName" :rules="[required]" autofocus color="green" label="Project Name"
                                  validate-on="blur"
                                  variant="outlined"></v-text-field>
                    <v-textarea v-model="projectDescription" auto-grow class="mt-3" color="green"
                                label="Project Description"
                                rows="3"
                                variant="outlined"></v-textarea>

                </v-form>

            </v-card-text>
            <v-card-actions>
                <v-btn block color="green" rounded size="large" variant="flat" @click="saveProject">
                    {{ editting ? "Update" : "Save" }} Project
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-snackbar v-model="success" color="green" dark>{{ success_message }}</v-snackbar>
    <v-snackbar v-model="error" color="error" dark>{{ error_message }}</v-snackbar>

</template>

<script>
import TasksComponent from "./TasksComponent";
import NoRecordsComponent from "./NoRecordsComponent";

export default {
    name: "ProjectsComponent",
    components: {NoRecordsComponent, TasksComponent},
    data() {
        return {
            projects: [],
            showCreateProjectModal: false,
            loadingProjects: false,
            projectName: "",
            projectDescription: "",
            savingProject: false,
            selectedProject: null,
            showDeleteProject: false,
            success: false,
            success_message: "",
            error: false,
            error_message: "",
            editting: false
        }
    },
    methods: {
        required(v) {
            return !!v || 'Project name is required'
        },
        saveProject() {

            if (this.$refs.projectForm.validate()) {
                this.savingProject = true;
                const data = {
                    name: this.projectName,
                    description: this.projectDescription
                };

                const url = this.editting ? "/api/projects/" + this.selectedProject.id : "/api/projects";

                axios.post(url, data)
                    .then(response => {
                        this.getProjects();
                        this.showCreateProjectModal = false;
                        this.savingProject = false;
                        this.projectName = "";
                        this.projectDescription = "";
                        this.selectedProject = response.data.data;

                    })
                    .catch(error => {
                        this.savingProject = false;

                    })


            }

        },
        getProjects() {
            this.loadingProjects = true;
            axios.get("/api/projects")
                .then(response => {

                    this.projects = response.data.data;
                    this.loadingProjects = false;

                })
        },
        deleteProject() {
            this.loadingProjects = true;
            axios.delete("/api/projects/" + this.selectedProject.id)
                .then(response => {

                    this.loadingProjects = false;

                    this.success_message = "Project deleted";
                    this.success = true;

                    this.selectedProject = null;
                    this.showDeleteProject = false;
                    this.getProjects();
                })
                .catch(error => {
                    this.loadingProjects = false;

                    this.error_message = error.response.data.message ? error.response.data.message : "Error: could not delete project";
                    this.error = true;
                })

        }
    },
    mounted() {
        this.getProjects();
    }
}
</script>

<style>
.rounded {
    border-radius: 15px !important;
}


</style>

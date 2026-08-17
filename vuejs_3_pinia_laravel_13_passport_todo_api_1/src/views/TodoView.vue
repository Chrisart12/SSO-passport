<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useTodoStore } from "@/stores/todo";

const todoStore = useTodoStore();
const router = useRouter();
const submitError = ref(null);

const todos = computed(() => todoStore.todos);

const formatDateForInput = (value) => {
    if (!value) return "";
    return String(value).slice(0, 10);
};

const removeTodo = async (id) => {
    const err = await todoStore.deleteTodo(id);
    if (err) {
        submitError.value = err;
    }
};

const toggleCompletion = async (todo) => {
    const payload = {
        title: todo.title,
        description: todo.description,
        due_date: todo.due_date,
        is_completed: !todo.is_completed,
    };

    const err = await todoStore.editTodo(todo.id, payload);
    if (err) {
        submitError.value = err;
    }
};

onMounted(() => {
    todoStore.fetchTodos();
});

const goToCreate = () => {
    router.push({ name: "todo-create" });
};

const goToEdit = (id) => {
    router.push({ name: "todo-edit", params: { id } });
};
</script>

<template>
    <section class="todo-page">
        <div class="page-header">
            <h1>Liste des tâches</h1>
            <button type="button" class="btn btn-create" @click="goToCreate">
                Créer
            </button>
        </div>

        <p v-if="submitError" class="error-message">{{ submitError }}</p>
        <p v-if="todoStore.error" class="error-message">{{ todoStore.error }}</p>

        <p v-if="todoStore.isLoadingTodos">Chargement des tâches...</p>

        <ul v-else class="todo-list">
            <li v-for="todo in todos" :key="todo.id" class="todo-item">
                <div>
                    <h3>
                        {{ todo.title }}
                        <small v-if="todo.is_completed">(Terminée)</small>
                    </h3>
                    <p>{{ todo.description }}</p>
                    <p v-if="todo.due_date">Échéance: {{ formatDateForInput(todo.due_date) }}</p>
                </div>

                <div class="todo-actions">
                    <button type="button" class="btn btn-toggle" @click="toggleCompletion(todo)">
                        {{ todo.is_completed ? "Marquer en cours" : "Marquer terminée" }}
                    </button>
                    <button type="button" class="btn btn-edit" @click="goToEdit(todo.id)">
                        Modifier
                    </button>
                    <button type="button" class="btn btn-delete" @click="removeTodo(todo.id)">
                        Supprimer
                    </button>
                </div>
            </li>
        </ul>
    </section>
</template>

<style scoped>
.todo-page {
    max-width: 900px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.todo-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 10px;
}

.todo-item {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 12px;
    display: flex;
    justify-content: space-between;
    gap: 12px;
}

.todo-actions {
    display: grid;
    gap: 8px;
    align-content: start;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 9px 12px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.btn-create {
    background: #0f9d58;
}

.btn-toggle {
    background: #d97706;
}

.btn-edit {
    background: #2563eb;
}

.btn-delete {
    background: #dc2626;
}

.error-message {
    color: #b42318;
}
</style>

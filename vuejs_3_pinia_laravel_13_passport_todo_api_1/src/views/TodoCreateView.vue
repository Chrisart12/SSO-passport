<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useTodoStore } from "@/stores/todo";

const router = useRouter();
const todoStore = useTodoStore();

const form = ref({
    title: "",
    description: "",
    due_date: "",
    is_completed: false,
});

const submitError = ref(null);

const submitTodo = async () => {
    submitError.value = null;

    const payload = {
        title: form.value.title,
        description: form.value.description,
        due_date: form.value.due_date || null,
        is_completed: Boolean(form.value.is_completed),
    };

    const err = await todoStore.createTodo(payload);
    if (err) {
        submitError.value = err;
        return;
    }

    router.push({ name: "todo" });
};
</script>

<template>
    <section class="todo-page">
        <h1>Créer une tâche</h1>

        <form class="todo-form" @submit.prevent="submitTodo">
            <label>
                Titre
                <input v-model="form.title" type="text" required />
            </label>

            <label>
                Description
                <textarea v-model="form.description" rows="3"></textarea>
            </label>

            <label>
                Date d'échéance
                <input v-model="form.due_date" type="date" />
            </label>

            <label class="inline-checkbox">
                <input v-model="form.is_completed" type="checkbox" />
                Terminée
            </label>

            <div class="form-actions">
                <button type="submit" class="btn btn-create">Créer</button>
                <button type="button" class="btn btn-cancel" @click="router.push({ name: 'todo' })">
                    Annuler
                </button>
            </div>
        </form>

        <p v-if="submitError" class="error-message">{{ submitError }}</p>
        <p v-if="todoStore.error" class="error-message">{{ todoStore.error }}</p>
    </section>
</template>

<style scoped>
.todo-page {
    max-width: 760px;
    margin: 0 auto;
}

.todo-form {
    display: grid;
    gap: 12px;
    margin-top: 12px;
}

.todo-form label {
    display: grid;
    gap: 6px;
}

.inline-checkbox {
    display: flex !important;
    align-items: center;
    gap: 8px;
}

.form-actions {
    display: flex;
    gap: 10px;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 10px 14px;
    color: #fff;
    cursor: pointer;
    font-weight: 600;
}

.btn-create {
    background: #0f9d58;
}

.btn-cancel {
    background: #6b7280;
}

.error-message {
    color: #b42318;
}
</style>

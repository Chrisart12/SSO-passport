import { defineStore } from "pinia";
import { computed, ref } from "vue";
import axios from "@/services/axios";
import { extractErrors } from "@/utils/extractErrors";

export const useTodoStore = defineStore("todo", () => {
    const todos = ref([]);
    const error = ref(null);
    const isLoadingTodos = ref(false);

    const normalizeTodos = (payload) => {
        if (Array.isArray(payload)) return payload;
        if (Array.isArray(payload?.data)) return payload.data;
        return [];
    };

    const getTodos = computed(() => todos.value);

    const resetTodos = () => {
        todos.value = [];
    };

    const fetchTodos = async () => {
        error.value = null;
        isLoadingTodos.value = true;

        try {
            const response = await axios.get("/tasks");
            todos.value = normalizeTodos(response.data);
        } catch (err) {
            error.value = extractErrors(err);
        } finally {
            isLoadingTodos.value = false;
        }
    };

    const createTodo = async (payload) => {
        error.value = null;

        try {
            const response = await axios.post("/tasks", payload);
            todos.value = normalizeTodos(todos.value);
            todos.value.push(response.data);
            return null;
        } catch (err) {
            const extracted = extractErrors(err);
            error.value = extracted;
            return extracted;
        }
    };

    const editTodo = async (id, payload) => {
        error.value = null;

        try {
            const response = await axios.put(`/tasks/${id}`, payload);
            const updatedTodo = response.data;
            todos.value = normalizeTodos(todos.value);
            todos.value = todos.value.map((todo) =>
                todo.id === id ? updatedTodo : todo
            );
            return null;
        } catch (err) {
            const extracted = extractErrors(err);
            error.value = extracted;
            return extracted;
        }
    };

    const deleteTodo = async (id) => {
        error.value = null;

        try {
            await axios.delete(`/tasks/${id}`);
            todos.value = normalizeTodos(todos.value);
            todos.value = todos.value.filter((todo) => todo.id !== id);
            return null;
        } catch (err) {
            const extracted = extractErrors(err);
            error.value = extracted;
            return extracted;
        }
    };

    return {
        todos: getTodos,
        error,
        isLoadingTodos,
        fetchTodos,
        createTodo,
        editTodo,
        deleteTodo,
        resetTodos,
    };
});

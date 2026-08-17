import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { useTodoStore } from "@/stores/todo";
import axios from "@/services/axios";

vi.mock("@/services/axios", () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
    },
}));

describe("useTodoStore", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        vi.clearAllMocks();
    });

    it("fetches todos from API", async () => {
        const store = useTodoStore();
        axios.get.mockResolvedValue({
            data: [
                { id: 1, title: "Task 1" },
                { id: 2, title: "Task 2" },
            ],
        });

        await store.fetchTodos();

        expect(axios.get).toHaveBeenCalledWith("/tasks");
        expect(store.todos).toHaveLength(2);
        expect(store.error).toBeNull();
    });

    it("creates a todo", async () => {
        const store = useTodoStore();
        axios.post.mockResolvedValue({
            data: { id: 10, title: "New Task", is_completed: false },
        });

        const result = await store.createTodo({ title: "New Task" });

        expect(result).toBeNull();
        expect(axios.post).toHaveBeenCalledWith("/tasks", { title: "New Task" });
        expect(store.todos).toEqual([{ id: 10, title: "New Task", is_completed: false }]);
    });

    it("updates a todo", async () => {
        const store = useTodoStore();
        axios.post.mockResolvedValueOnce({
            data: { id: 4, title: "Old", is_completed: false },
        });
        await store.createTodo({ title: "Old" });

        axios.put.mockResolvedValue({
            data: { id: 4, title: "Updated", is_completed: true },
        });

        const result = await store.editTodo(4, { title: "Updated", is_completed: true });

        expect(result).toBeNull();
        expect(axios.put).toHaveBeenCalledWith("/tasks/4", {
            title: "Updated",
            is_completed: true,
        });
        expect(store.todos[0].title).toBe("Updated");
        expect(store.todos[0].is_completed).toBe(true);
    });

    it("deletes a todo", async () => {
        const store = useTodoStore();
        axios.post.mockResolvedValueOnce({ data: { id: 1, title: "A" } });
        axios.post.mockResolvedValueOnce({ data: { id: 2, title: "B" } });
        await store.createTodo({ title: "A" });
        await store.createTodo({ title: "B" });

        axios.delete.mockResolvedValue({});

        const result = await store.deleteTodo(1);

        expect(result).toBeNull();
        expect(axios.delete).toHaveBeenCalledWith("/tasks/1");
        expect(store.todos).toEqual([{ id: 2, title: "B" }]);
    });

    it("returns extracted errors on create failure", async () => {
        const store = useTodoStore();
        axios.post.mockRejectedValue({
            response: {
                data: {
                    message: "Validation failed",
                },
            },
        });

        const result = await store.createTodo({ title: "" });

        expect(result).toBe("Validation failed");
        expect(store.error).toBe("Validation failed");
    });
});

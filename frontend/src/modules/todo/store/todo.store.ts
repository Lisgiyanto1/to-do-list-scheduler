import { defineStore } from "pinia";

export const useTodoStore = defineStore("todo", {
    state: () => ({

        search: "",
        selectedAssigneeId: null as string | null,


        sortBy: "created_at",
        sortOrder: "desc" as "asc" | "desc",


        page: 1,
        perPage: 10,

        updatingField: {} as Record<string, boolean>,
    }),

    actions: {
        setSearch(value: string) {
            this.search = value;
            this.page = 1;
        },

        setFilter(assigneeId: string | null) {

            this.selectedAssigneeId = this.selectedAssigneeId === assigneeId ? null : assigneeId;
            this.page = 1;
        },

        setSort(field: string) {
            if (this.sortBy === field) {
                this.sortOrder = this.sortOrder === "asc" ? "desc" : "asc";
            } else {
                this.sortBy = field;
                this.sortOrder = "desc";
            }
            this.page = 1;
        },

        setPage(p: number) {
            this.page = p;
        },

        setPerPage(p: number) {
            this.perPage = p;
            this.page = 1;
        },

        setFieldLoading(id: string, field: string, isLoading: boolean) {
            const key = `${id}_${field}`;
            if (isLoading) {
                this.updatingField[key] = true;
            } else {
                delete this.updatingField[key];
            }
        },

        isFieldLoading(id: string, field: string) {
            return !!this.updatingField[`${id}_${field}`];
        }
    },
});
export type TodoStatus =
    | "Ready to start"
    | "In Progress"
    | "Waiting for review"
    | "Pending Deploy"
    | "Done"
    | "Stuck";

export type Priority =
    | "Critical"
    | "High"
    | "Medium"
    | "Low"
    | "Best Effort";

export type TodoType =
    | "Feature Enhancements"
    | "Other"
    | "Bug";

export interface Todo {
    id: string;
    task: string;
    developers: string[];
    status: TodoStatus;
    time_tracked: number;
    priority: Priority;
    type: TodoType;
    date: string;
    estimated_sp: number;
    actual_sp: number;
}
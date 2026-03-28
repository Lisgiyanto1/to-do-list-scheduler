export const getColorClass = (val: string, category: string) => {
    if (!val) return 'bg-slate-600';

    const statusKey = val.toLowerCase().replace(/_/g, ' ').trim();

    if (category === 'status') {
        if (statusKey === 'ready to start') return 'bg-blue-600';
        if (statusKey === 'in progress' || statusKey === 'in_progress') return 'bg-amber-500';
        if (statusKey === 'waiting for review') return 'bg-sky-400';
        if (statusKey === 'pending deploy') return 'bg-indigo-500';
        if (statusKey === 'done' || statusKey === 'completed') return 'bg-green-500';
        if (statusKey === 'stuck') return 'bg-red-500';
        if (statusKey === 'pending') return 'bg-slate-500'; // Sesuai JSON backend Anda sebelumnya
    }

    if (category === 'priority') {
        if (statusKey === 'critical') return 'bg-red-600';
        if (statusKey === 'high') return 'bg-purple-500';
        if (statusKey === 'medium') return 'bg-blue-400';
        if (statusKey === 'low') return 'bg-emerald-500';
    }

    if (category === 'type') {
        if (statusKey === 'feature' || statusKey === 'feature') return 'bg-pink-300';
        if (statusKey === 'bug') return 'bg-rose-500';
        if (statusKey === 'other') return 'bg-violet-400';
    }

    return 'bg-slate-600';
};
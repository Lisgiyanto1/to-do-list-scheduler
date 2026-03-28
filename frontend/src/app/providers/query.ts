export const queryClientConfig = {
    queryClientConfig: {
        defaultOptions: {
            queries: {
                staleTime: 1000 * 60 * 5,
                refetchOnWindowFocus: false,
                retry: 1,
            },
        },
    },
};
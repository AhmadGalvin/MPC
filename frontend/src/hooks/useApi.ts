import { useState, useCallback } from 'react';
import axios, { AxiosError } from 'axios';
import { toast } from 'react-hot-toast';

interface ApiOptions {
    showSuccessToast?: boolean;
    showErrorToast?: boolean;
    successMessage?: string;
    errorMessage?: string;
    retries?: number;
    retryDelay?: number;
}

interface ApiState<T> {
    data: T | null;
    loading: boolean;
    error: Error | null;
}

export function useApi<T = any>(defaultOptions: ApiOptions = {}) {
    const [state, setState] = useState<ApiState<T>>({
        data: null,
        loading: false,
        error: null,
    });

    const makeRequest = useCallback(async (
        apiCall: () => Promise<T>,
        options: ApiOptions = {}
    ) => {
        const {
            showSuccessToast = false,
            showErrorToast = true,
            successMessage = 'Operation successful',
            errorMessage = 'An error occurred',
            retries = 3,
            retryDelay = 1000,
        } = { ...defaultOptions, ...options };

        setState(prev => ({ ...prev, loading: true, error: null }));

        let attempt = 0;
        while (attempt < retries) {
            try {
                const response = await apiCall();
                setState({ data: response, loading: false, error: null });
                
                if (showSuccessToast) {
                    toast.success(successMessage);
                }
                
                return response;
            } catch (error) {
                attempt++;
                
                if (attempt === retries) {
                    const axiosError = error as AxiosError;
                    const errorMsg = axiosError.response?.data?.message || errorMessage;
                    
                    setState({
                        data: null,
                        loading: false,
                        error: new Error(errorMsg)
                    });

                    if (showErrorToast) {
                        toast.error(errorMsg);
                    }
                    
                    throw error;
                }

                // Wait before retrying
                await new Promise(resolve => setTimeout(resolve, retryDelay * attempt));
            }
        }
    }, [defaultOptions]);

    const reset = useCallback(() => {
        setState({
            data: null,
            loading: false,
            error: null,
        });
    }, []);

    return {
        ...state,
        makeRequest,
        reset,
    };
} 
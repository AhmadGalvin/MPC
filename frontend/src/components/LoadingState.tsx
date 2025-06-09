import React from 'react';

interface LoadingStateProps {
    message?: string;
    size?: 'sm' | 'md' | 'lg';
    fullScreen?: boolean;
}

const LoadingState: React.FC<LoadingStateProps> = ({
    message = 'Loading...',
    size = 'md',
    fullScreen = false,
}) => {
    const spinnerSizes = {
        sm: 'h-4 w-4',
        md: 'h-8 w-8',
        lg: 'h-12 w-12',
    };

    const containerClasses = fullScreen
        ? 'fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50'
        : 'flex items-center justify-center p-4';

    return (
        <div className={containerClasses}>
            <div className="text-center">
                <div
                    className={`animate-spin rounded-full border-4 border-primary-200 border-t-primary-600 ${spinnerSizes[size]} mx-auto`}
                />
                {message && (
                    <p className="mt-2 text-gray-600">{message}</p>
                )}
            </div>
        </div>
    );
};

export default LoadingState; 
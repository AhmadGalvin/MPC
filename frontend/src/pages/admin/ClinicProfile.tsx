import React, { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import axios from 'axios';
import { toast } from 'react-toastify';

interface ClinicData {
  name: string;
  address: string;
  phone_number: string;
  email: string;
  description: string;
  logo_path: string | null;
}

const ClinicProfile: React.FC = () => {
  const [loading, setLoading] = useState(true);
  const [previewImage, setPreviewImage] = useState<string | null>(null);
  
  const { register, handleSubmit, setValue, formState: { errors } } = useForm<ClinicData>();

  useEffect(() => {
    const fetchClinicProfile = async () => {
      try {
        const response = await axios.get('/api/admin/clinic');
        const clinic = response.data.data;
        
        // Set form values
        setValue('name', clinic.name);
        setValue('address', clinic.address);
        setValue('phone_number', clinic.phone_number);
        setValue('email', clinic.email);
        setValue('description', clinic.description || '');
        
        if (clinic.logo_path) {
          setPreviewImage(`/storage/${clinic.logo_path}`);
        }
      } catch (error) {
        toast.error('Failed to load clinic profile');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchClinicProfile();
  }, [setValue]);

  const onSubmit = async (data: ClinicData) => {
    try {
      const formData = new FormData();
      Object.entries(data).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          formData.append(key, value);
        }
      });

      // Handle logo file
      const logoInput = document.querySelector<HTMLInputElement>('input[name="logo"]');
      if (logoInput?.files?.[0]) {
        formData.append('logo', logoInput.files[0]);
      }

      await axios.put('/api/admin/clinic', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      toast.success('Clinic profile updated successfully');
    } catch (error) {
      toast.error('Failed to update clinic profile');
      console.error(error);
    }
  };

  const handleImageChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setPreviewImage(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  if (loading) {
    return <div className="flex justify-center items-center h-full">Loading...</div>;
  }

  return (
    <div className="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
      <h2 className="text-2xl font-bold mb-6">Clinic Profile</h2>
      
      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* Logo Upload */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Clinic Logo
          </label>
          <div className="flex items-center space-x-4">
            {previewImage && (
              <img
                src={previewImage}
                alt="Clinic Logo"
                className="w-24 h-24 object-cover rounded-lg"
              />
            )}
            <input
              type="file"
              accept="image/*"
              onChange={handleImageChange}
              name="logo"
              className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark"
            />
          </div>
        </div>

        {/* Name */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Clinic Name
          </label>
          <input
            type="text"
            {...register('name', { required: 'Clinic name is required' })}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
          />
          {errors.name && (
            <p className="mt-1 text-sm text-red-600">{errors.name.message}</p>
          )}
        </div>

        {/* Address */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Address
          </label>
          <textarea
            {...register('address', { required: 'Address is required' })}
            rows={3}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
          />
          {errors.address && (
            <p className="mt-1 text-sm text-red-600">{errors.address.message}</p>
          )}
        </div>

        {/* Phone Number */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Phone Number
          </label>
          <input
            type="tel"
            {...register('phone_number', { required: 'Phone number is required' })}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
          />
          {errors.phone_number && (
            <p className="mt-1 text-sm text-red-600">{errors.phone_number.message}</p>
          )}
        </div>

        {/* Email */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Email
          </label>
          <input
            type="email"
            {...register('email', {
              required: 'Email is required',
              pattern: {
                value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                message: 'Invalid email address',
              },
            })}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
          />
          {errors.email && (
            <p className="mt-1 text-sm text-red-600">{errors.email.message}</p>
          )}
        </div>

        {/* Description */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            {...register('description')}
            rows={4}
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
          />
        </div>

        {/* Submit Button */}
        <div>
          <button
            type="submit"
            className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          >
            Save Changes
          </button>
        </div>
      </form>
    </div>
  );
};

export default ClinicProfile; 